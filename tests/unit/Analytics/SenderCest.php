<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\DataTransfer\Streamer;
use Helper\Analytics\ProviderMock;
use Chocofamily\Analytics\SenderValidator;

class SenderCest
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
                'info'       => 'astana',
            ],
        ];

        $validator        = new SenderValidator($data);
        $sender           = new Streamer($validator);
        $sender->transfer = new ProviderMock();

        $result = $helper->invokeMethod($sender, 'prepare', [$data]);

        $I->assertEquals([
            [
                'insertId' => $data[0]['uuid'],
                'data'     => $data[0],
            ],
        ], $result);
    }
}
