<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;


class Query extends Transfer
{
    /**
     * @var string
     */
    private $sql = '';

    /**
     * @return bool
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function execute(): bool
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
        if (!$this->queryContainsLimit($sql)) {
            $sql .= self::LIMIT_STRING.self::LIMIT_NUMBER;
        }
        $this->sql = $sql;
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