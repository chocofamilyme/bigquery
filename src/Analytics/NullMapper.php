<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

class NullMapper implements MapperInterface
{
    public function process(array &$data)
    {
    }
}
