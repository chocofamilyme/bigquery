<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */
namespace Helper\Analytics;

class ProviderMock implements \Chocofamily\Analytics\Providers\ProviderInterface
{
    private $tableName;

    /**
     * @var \Exception
     */
    public $thrownException = null;

    public function send(): bool
    {
        return true;
    }

    public function exists(): bool
    {
        // TODO: Implement exists() method.
    }

    public function createTable(string $name, array $options)
    {
        // TODO: Implement createTable() method.
    }

    public function getErrors(): array
    {
        return [];
    }

    public function addErrors(string $id, string $message)
    {
        // TODO: Implement addErrors() method.
    }

    public function clearErrors()
    {
        // TODO: Implement clearErrors() method.
    }

    public function setTable(string $table)
    {
        $this->tableName = $table;
    }

    public function getTable()
    {
        // TODO: Implement getTable() method.
    }

    public function getTableName()
    {
        return $this->tableName;
    }
}
