<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\UndeliveredDataStorage;
use Helper\Analytics\Models\UndeliveredDataMock;

class UndeliveredDataCest
{
    public function tryToStoredData(\UnitTester $I)
    {
        $I->wantToTest('Сохранить данные');

        $undeliveredDataStorage = new UndeliveredDataStorage(UndeliveredDataMock::class, 'test');
        $undeliveredDataStorage->insert(['']);

        $I->assertTrue(UndeliveredDataMock::$saved);
    }

    public function tryToThroughException(\UnitTester $I)
    {
        $I->wantToTest('Выбросить исключение');

        $undeliveredDataStorage = new UndeliveredDataStorage(null, 'test');

        $I->expectException(
            ValidationException::class,
            function () use ($undeliveredDataStorage) {
                $undeliveredDataStorage->insert(['']);
            }
        );
    }
}
