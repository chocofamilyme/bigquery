<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Providers\StreamerInterface;
use Google\Cloud\BigQuery\InsertResponse;

/**
 * Class Streamer отправляет данные по стрпочно в BigQuery
 * Рекомендации https://cloud.google.com/bigquery/quotas#streaming_inserts
 *
 * @package Chocofamily\Analytics\Providers\BigQuery
 */
class Streamer extends Transfer implements StreamerInterface
{
    /**
     * @var array
     */
    private $rows = [];

    public function setRows(array $rows): void
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
