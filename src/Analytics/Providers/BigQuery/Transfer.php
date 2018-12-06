<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Providers\ProviderInterface;
use Google\Cloud\BigQuery\Table;
use Phalcon\Config;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

/**
 * Class BigQuery
 *
 * Провайдера для работы с Google BigQuery
 *
 * @package Chocofamily\Analytics\Providers
 */
abstract class Transfer implements ProviderInterface
{

    const DEFAULT_OPTIONS = [
        'skipInvalidRows'     => true,
        'ignoreUnknownValues' => true,
    ];

    const LIMIT_NUMBER = 100;
    const LIMIT_STRING = ' LIMIT ';

    private $config;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var BigQueryClient
     */
    protected $client;

    /**
     * @var \Google\Cloud\BigQuery\Dataset
     */
    private $dataSet;

    /** @var Table */
    private $table;

    /**
     * @var int
     */
    protected $attempt = 5;

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

        $this->attempt = $config['repeater']->get('attempt', 5);
        $this->dataSet = $this->client->dataset($config->get('dataset'));
    }

    abstract public function execute(): bool;

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
