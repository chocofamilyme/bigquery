<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Chocofamily\Analytics\Providers\ProviderInterface;

interface StreamBufferInterface
{

    public function addBuffer(string $tableName, array $row): void;

    public function run(ProviderInterface $provider): bool;
}
