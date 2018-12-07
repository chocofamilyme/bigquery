<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Google\Cloud\BigQuery\InsertResponse;

class Streamer extends Transfer
{
    /**
     * @var array
     */
    private $rows = [];

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
}
