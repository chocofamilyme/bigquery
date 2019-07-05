<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Providers\QueryInterface;

/**
 * Class Query выполняет sql запрос в BigQuery
 * Рекомендации https://cloud.google.com/bigquery/quotas#data_manipulation_language_statements
 *
 * @package Chocofamily\Analytics\Providers\BigQuery
 */
class Query extends Transfer implements QueryInterface
{
    /**
     * @var string
     */
    private $sql = '';

    /**
     * @return array
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function send(): array
    {
        $jobConfig   = $this->client->query($this->sql);
        $queryResult = $this->client->runQuery($jobConfig);

        return iterator_to_array($queryResult->rows());
    }

    /**
     * @param mixed $sql
     */
    public function setSql(string $sql): void
    {
        $this->sql = $sql;
    }
}
