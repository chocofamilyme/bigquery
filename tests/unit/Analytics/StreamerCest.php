<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\DataTransfer\StreamerWrapper;
use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\DataValidator;
use Helper\Analytics\Models\UndeliveredDataMock;
use Helper\Analytics\StreamerMock;

class StreamerCest
{
    /**
     * @var StreamerWrapper
     */
    private $streamer;

    public function setUp()
    {
        $data        = [
            [
                'uuid'       => '1',
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'test',
            ],
        ];
        $mapperClass = \Phalcon\Di::getDefault()->getShared('config')->analytics->mappers->tableName;

        $validator      = new DataValidator($data);
        $this->streamer = new StreamerWrapper($validator);

        $this->streamer->transfer = new StreamerMock();
        $this->streamer->setMapper(new $mapperClass);
        $this->streamer->validator->setClientData($data);
    }

    public function tryToSendDataWithoutException(\UnitTester $I)
    {
        $I->wantToTest('Отправить данные без исключении');

        $I->assertNull($this->streamer->send());
    }

    public function tryToThrowException(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Выкинуть исключение');

        $exception = new ValidationException('test');
        $helper->invokeProperty(
            $this->streamer,
            'excludeExceptions',
            [get_class($exception)]
        );
        $this->streamer->transfer->throwException($exception);

        $I->expectException($exception, function () {
            $this->streamer->send();
        });
    }

    public function tryToStoreUndeliveredData(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Сохранить недоставленные данные');

        $exception = new ValidationException('test');
        $helper->invokeProperty(
            $this->streamer,
            'excludeExceptions',
            []
        );
        $this->streamer->transfer->throwException($exception);
        $this->streamer->transfer->setTable('test_table');

        $I->expectException($exception, function () {
            $this->streamer->send();
        });

        $I->assertTrue(UndeliveredDataMock::$saved);
    }
}
