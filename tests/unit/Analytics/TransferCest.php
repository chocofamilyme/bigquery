<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\DataValidator;
use Helper\Analytics\Models\DeliveryMock;

class TransferCest
{
    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToPrepare(\UnitTester $I, \Helper\Unit $helper)
    {
        $data = [
            [
                'uuid'       => '1',
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'test',
            ],
        ];

        $validator = new DataValidator($data);
        $streamer  = new DeliveryMock($validator);
        $result = $helper->invokeMethod($streamer, 'prepare', [$data]);

        $I->assertEquals([
            [
                'insertId' => $data[0]['uuid'],
                'data'     => $data[0],
            ],
        ], $result);
    }
}
