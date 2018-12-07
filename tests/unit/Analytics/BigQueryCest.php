<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Providers\BigQuery\Streamer;
use Helper\Analytics\Models\UndeliveredDataMock;

class BigQueryCest
{

    /** @var Streamer */
    private $streamer;

    private $attempt = 1;

    /**
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function setUp(\Helper\Unit $helper)
    {
        $analytics      = \Phalcon\Di::getDefault()->getShared('config')->analytics;
        $this->streamer = new Streamer($analytics);

        $helper->invokeProperty($this->streamer, 'attempt', $this->attempt);
    }

    /**
     * @param \UnitTester $I
     */
    public function tryToThrowTableIsNullException(\UnitTester $I)
    {
        $I->wantToTest('Правило на отсутсвие названия таблицы');

        $providerWrapper = $this->streamer;
        $providerWrapper->setRows([]);

        $I->expectException(
            new ValidationException('Укажите таблицу'),
            function () use ($providerWrapper) {
                $providerWrapper->execute();
            }
        );
    }
}
