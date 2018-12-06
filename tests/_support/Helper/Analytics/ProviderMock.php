<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */
namespace Helper\Analytics;

class ProviderMock implements \Chocofamily\Analytics\ProviderInterface
{

    public function insert(array $rows)
    {
        // TODO: Implement insert() method.
    }

    public function load(string $file): bool
    {
        // TODO: Implement load() method.
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
        // TODO: Implement getErrors() method.
    }

    public function clearErrors()
    {
        // TODO: Implement clearErrors() method.
    }

    public function setTable(string $table)
    {
        // TODO: Implement setTable() method.
    }

    public function addErrors(string $id, string $message)
    {
        // TODO: Implement addErrors() method.
    }

    public function getTableName()
    {
        // TODO: Implement getTableName() method.
    }

    public function getConfig()
    {
        // TODO: Implement getConfig() method.
    }

    public function getTable()
    {
        // TODO: Implement getTable() method.
    }
}
