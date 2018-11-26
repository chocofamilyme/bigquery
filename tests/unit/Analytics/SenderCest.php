<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Helper\Analytics\ConfigMock;
use Helper\Analytics\ProviderMock;
use Chocofamily\Analytics\Sender;
use Chocofamily\Analytics\SenderValidator;

class SenderCest
{
    public function tryToPrepare(\UnitTester $I)
    {
        $data = [
            [
                'uuid'       => '1',
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'astana',
            ],
        ];

        $validator        = new SenderValidator($data);
        $sender           = new Sender($validator);
        $sender->provider = new ProviderMock();
        $result           = $sender->prepare($data);

        $I->assertEquals([
            [
                'insertId' => $data[0]['uuid'],
                'data'     => $data[0],
            ],
        ], $result);
    }
}
