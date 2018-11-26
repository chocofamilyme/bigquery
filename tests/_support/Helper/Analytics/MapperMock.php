<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Helper\Analytics;

use Chocofamily\Analytics\MapperInterface;
use Phalcon\Di\Injectable;

class MapperMock extends Injectable implements MapperInterface
{

    public function process(array &$data)
    {
        // TODO: Implement process() method.
    }
}
