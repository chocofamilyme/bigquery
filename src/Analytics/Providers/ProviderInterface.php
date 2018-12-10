<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers;

interface ProviderInterface
{

    public function send(): bool;

    public function exists(): bool;

    public function createTable(string $name, array $options);

    public function getErrors(): array;

    public function addErrors(string $id, string $message);

    public function clearErrors();

    public function setTable(string $table);

    public function getTable();

    public function getTableName();
}
