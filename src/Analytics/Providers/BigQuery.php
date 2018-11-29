<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Services\UndeliveredData;
use Google\Cloud\BigQuery\Table;
use Phalcon\Config;
use Chocofamily\Analytics\ProviderInterface;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

/**
 * Class BigQuery
 *
 * Провайдера для работы с Google BigQuery
 *
 * @package Chocofamily\Analytics\Providers
 */
class BigQuery implements ProviderInterface
{

    const DEFAULT_OPTIONS = [
        'skipInvalidRows'     => true,
        'ignoreUnknownValues' => true,
    ];

    const MAX_DELAY_MICROSECONDS = 60000000;
    const LIMIT_NUMBER           = 100;
    const LIMIT_STRING           = ' LIMIT ';

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var BigQueryClient
     */
    private $client;

    /**
     * @var \Google\Cloud\BigQuery\Dataset
     */
    private $dataSet;

    /** @var Table */
    private $table;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var int
     */
    private $attempt = 5;

    /**
     * @var array
     */
    private $exclude = [];

    private $undeliveredDataModel;

    public function __construct(Config $config)
    {
        $this->client = new BigQueryClient([
            'keyFilePath' => $config->get('path'),
        ]);

        $this->attempt = $config['repeater']->get('attempt', 5);
        $this->exclude = $config['repeater']->get('exclude')->toArray();

        $this->dataSet              = $this->client->dataset($config->get('dataset'));
        $this->undeliveredDataModel = $config['undeliveredDataModel'];
    }

    /**
     * @param array $rows
     *
     * @return bool
     * @throws ValidationException
     * @throws \Exception
     */
    public function insert(array $rows)
    {
        if (empty($this->table)) {
            throw new ValidationException('Укажите таблицу');
        }

        $retryAttempt   = 0;
        $insertResponse = null;
        while (true) {
            try {
                $insertResponse = $this->table->insertRows($rows, self::DEFAULT_OPTIONS);
                break;
            } catch (\Exception $exception) {
                $this->checkRetry($exception, ++$retryAttempt, $rows);
                usleep($this->calculateDelay());
            }
        }

        if (!$insertResponse) {
            return false;
        }

        if (false == $insertResponse->isSuccessful()) {
            $this->collectErrors($insertResponse->failedRows());

            return false;
        }

        return $insertResponse->isSuccessful();
    }

    public function load(string $file): bool
    {
        if (empty($this->table)) {
            throw new ValidationException('Укажите таблицу');
        }

        $loadJobConfig = $this->table
            ->load(fopen($file, 'r'))
            ->ignoreUnknownValues(self::DEFAULT_OPTIONS['ignoreUnknownValues'])
            ->sourceFormat('NEWLINE_DELIMITED_JSON');

        $job = $this->table->runJob($loadJobConfig);

        $backoff = new ExponentialBackoff(3);

        $backoff->execute(function () use ($job) {
            $job->reload();
            if (!$job->isComplete()) {
                $this->addErrors(0, 'Job has not yet completed');
            }
        });

        if (isset($job->info()['status']['errorResult'])) {
            $reason = $job->info()['status']['errorResult']['reason'];
            $error  = $job->info()['status']['errorResult']['message'];
            $this->addErrors(1, $reason.': '.$error);
        }

        return $job->isComplete();
    }

    /**
     * @param $rawQuery
     *
     * @return array
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function runQuery($rawQuery): array
    {
        if (!$this->queryContainsLimit($rawQuery)) {
            $rawQuery .= self::LIMIT_STRING.self::LIMIT_NUMBER;
        }

        $jobConfig   = $this->client->query($rawQuery);
        $queryResult = $this->client->runQuery($jobConfig);

        return iterator_to_array($queryResult->rows());
    }


    public function exists(): bool
    {
        return $this->table->exists();
    }

    public function createTable(string $name, array $options)
    {
        $this->dataSet->createTable($name, $options);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function addErrors(string $id, string $message)
    {
        $this->errors[$id] = $message;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table     = $this->dataSet->table($table);
        $this->tableName = $table;
    }

    /**
     * Calculates exponential delay.
     *
     * @return int
     */
    private function calculateDelay()
    {
        return min(
            mt_rand(0, 1000000) + (pow(2, $this->attempt) * 1000000),
            self::MAX_DELAY_MICROSECONDS
        );
    }

    /**
     * @param $errors
     */
    private function collectErrors($errors)
    {
        foreach ($errors as $row) {
            $message = '';
            foreach ($row['errors'] as $error) {
                $message .= $error['reason'].': '.$error['message'].PHP_EOL;
            }
            $this->addErrors($row['rowData']['uuid'], $message);
        }
    }

    /**
     * Очищает массив с ошибками
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * @param \Exception $exception
     * @param int        $attempt
     * @param array      $rows
     *
     * @return bool
     * @throws \Exception
     */
    private function checkRetry(\Exception $exception, int $attempt, array $rows)
    {
        $attemptsExceeded = $this->attemptsExceeded($attempt);
        if (in_array(get_class($exception), $this->exclude) or $attemptsExceeded) {
            if ($attemptsExceeded) {
                $this->createUndeliveredData($rows);
            }
            throw $exception;
        }

        return true;
    }

    /**
     * @param $attempt
     *
     * @return bool
     */
    private function attemptsExceeded($attempt)
    {
        return $attempt >= $this->attempt;
    }

    /**
     * @param array $rows
     *
     * @throws ValidationException
     * @throws \Chocofamily\Analytics\Exceptions\ClassNotFound
     */
    private function createUndeliveredData(array $rows)
    {
        if ($this->undeliveredDataModel === null) {
            throw new ValidationException(
                'Укажите модель для записи недоставленных данных в analytics, по ключу `undeliveredDataModel`'
            );
        }
        $undeliveredService = new UndeliveredData($this->undeliveredDataModel);
        $tableName          = $this->tableName;
        $data               = \json_encode($rows);
        $undeliveredService->create($tableName, $data);
    }

    /**
     * @param $rawQuery
     *
     * @return bool
     */
    private function queryContainsLimit($rawQuery)
    {
        return strpos(mb_strtolower($rawQuery), strtolower(self::LIMIT_STRING)) !== false;
    }
}
