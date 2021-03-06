<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers;

interface StreamerInterface extends ProviderInterface
{
    public function setRows(array $rows): void;
}
