<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Services\UndeliveredData;
use Google\Cloud\BigQuery\InsertResponse;
use Phalcon\Config;

class Streamer extends Transfer
{

    private $undeliveredDataModel;

    /**
     * @var array
     */
    private $rows = [];

    public function __construct(Config $config)
    {
        parent::__construct($config);

        $this->undeliveredDataModel = $config['undeliveredDataModel'];
        //$this->exclude              = $config['repeater']->get('exclude')->toArray();
    }

    public function setRows(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function execute(): bool
    {
        if (empty($this->getTable())) {
            throw new ValidationException('Укажите таблицу');
        }

              /** @var InsertResponse $insertResponse */
        $insertResponse = $this->getTable()->insertRows($this->rows, self::DEFAULT_OPTIONS);

        if (false == $insertResponse->isSuccessful()) {
            $this->collectErrors($insertResponse->failedRows());

            return false;
        }

        return true;
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
        $tableName          = $this->getTableName();
        $data               = \json_encode($rows);
        $undeliveredService->create($tableName, $data);
    }
}
