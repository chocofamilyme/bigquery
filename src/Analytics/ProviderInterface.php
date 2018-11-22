<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

interface ProviderInterface
{

    public function insert(array $rows);

    public function exists(): bool;

    public function createTable(string $name, array $options);

    public function getErrors(): array;

    public function setTable(string $table);
}
