<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Providers\BigQuery\Streamer;

class BigQueryCest
{
    /**
     * @param \UnitTester $I
     */
    public function tryToThrowTableIsNullException(\UnitTester $I)
    {
        $I->wantToTest('Правило на отсутсвие названия таблицы');

        $analytics      = \Phalcon\Di::getDefault()->getShared('config')->get('analytics');

        $providerWrapper = new Streamer($analytics->toArray());
        $providerWrapper->setRows([]);

        $I->expectException(
            new ValidationException('Укажите таблицу'),
            function () use ($providerWrapper) {
                $providerWrapper->send();
            }
        );
    }
}
