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

    const LIMIT_NUMBER           = 100;
    const LIMIT_STRING           = ' LIMIT ';

    private $config;

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

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new BigQueryClient([
            'keyFilePath' => $config->get('path'),
        ]);

        $this->dataSet              = $this->client->dataset($config->get('dataset'));
    }

    public function getConfig()
    {
        return $this->config;
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
        return $this->table->insertRows($rows, self::DEFAULT_OPTIONS);
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

    public function getTableName()
    {
        return $this->tableName;
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

    /**
     * Очищает массив с ошибками
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    public function getTable()
    {
        return $this->table;
    }
}
