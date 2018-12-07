<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Services\UndeliveredData;

class UndeliveredDataStorage
{
    private $undeliveredDataModel;

    private $tableName;

    public function __construct($undeliveredDataModel, $tableName)
    {
        $this->undeliveredDataModel = $undeliveredDataModel;
        $this->tableName = $tableName;
    }

    /**
     * @param array $rows
     *
     * @throws ValidationException
     * @throws \Chocofamily\Analytics\Exceptions\ClassNotFound
     */
    public function insert(array $rows)
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
}
