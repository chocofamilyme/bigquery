<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

interface ProviderInterface
{

    public function insert(array $rows);

    public function load(string $file);

    public function exists(): bool;

    public function createTable(string $name, array $options);

    public function getErrors(): array;

    public function addErrors(string $id, string $message);

    public function clearErrors();

    public function setTable(string $table);

    public function getTable();

    public function getTableName();

    public function getConfig();
}
