<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

interface RepeaterInterface
{
    public function calculateDelay(int $retryAttempt);

    public function run(callable $clientFunction, ...$arguments);
}
