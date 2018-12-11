<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\DataValidator;

class SenderValidatorCest
{
    public function tryToValidate(\UnitTester $I, \Helper\Unit $helper)
    {
        $data = [
            [
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'astana',
            ]
        ];

        $senderValidator = new DataValidator($data);
        $validation = new ValidationException('The uuid is required');

        $I->expectException($validation, function () use ($helper, $senderValidator) {
            $helper->invokeMethod($senderValidator, 'validation');
        });
    }

    public function tryToFilter(\UnitTester $I, \Helper\Unit $helper)
    {
        $data = [
            [
                'uuid' => '1',
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'almaty',
            ],
            [
                'uuid' => '2',
                'info'       => 'astana',
            ],
        ];
        $badMessages = [
            '2' => 'created_at is required'
        ];

        $senderValidator = new DataValidator($data);

        $helper->invokeProperty($senderValidator, 'badMessages', $badMessages);
        $result = $helper->invokeMethod($senderValidator, 'filter');

        $I->assertEquals([$data[0]], $result);
    }
}
