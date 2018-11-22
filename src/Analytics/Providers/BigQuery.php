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
    const LIMIT_NUMBER = 100;
    const LIMIT_STRING = ' LIMIT ';

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

    private $undeliveredService;

    public function __construct(Config $config)
    {
        $this->client = new BigQueryClient([
            'keyFilePath' => $config->get('path'),
        ]);

        $this->attempt = $config['repeater']->get('attempt', 5);
        $this->exclude = $config['repeater']->get('exclude')->toArray();

        $this->dataSet = $this->client->dataset($config->get('dataset'));
        $this->undeliveredService = new UndeliveredData($config['undeliveredDataModel']);
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

    /**
     * @param $rawQuery
     *
     * @return array
     * @throws ValidationException
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function runQuery($rawQuery): array
    {
        if (!$this->queryContainsLimit($rawQuery)) {
            $rawQuery .= self::LIMIT_STRING.self::LIMIT_NUMBER;
        }

        $jobConfig = $this->client->query($rawQuery);
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
    public function setErrors(array $errors): void
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
     */
    private function createUndeliveredData(array $rows)
    {
        $tableName       = $this->tableName;
        $data            = json_encode($rows);
        $this->undeliveredService->create($tableName, $data);
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
