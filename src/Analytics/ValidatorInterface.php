<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

interface ValidatorInterface
{
    public function check(): array;

    public function setClientData(array $data);
}
