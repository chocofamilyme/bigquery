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

    /**
     * Проверить сработает повтор
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToCheckRetryAttempt(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Повтор запроса');

        $exp = new ValidationException('тест');

        $I->assertTrue($helper->invokeMethod($this->streamer, 'checkRetry', [$exp, 0, ['test' => 'data']]));
    }

    /**
     * Проверить сработает исключение на превышения числа попыток
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     */
    public function tryToCheckRetryExceptionAttempt(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Выбросить исключение при привышении кол-ва попыток');

        $providerWrapper = $this->streamer;
        $tableName       = 'test_table';
        $providerWrapper->setTable($tableName);

        $exp = new ValidationException('тест');


        $I->expectException(
            $exp,
            function () use ($helper, $providerWrapper, $exp) {
                $helper->invokeMethod($providerWrapper, 'checkRetry', [$exp, $this->attempt, ['test' => 'data']]);
            }
        );
    }

    /**
     * Проверить сработает исключение на Exception
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToRetryException(\UnitTester $I, \Helper\Unit $helper)
    {
        $exclude = [
            ValidationException::class,
        ];

        $exp = new ValidationException('тест');

        $providerWrapper = $this->streamer;
        $helper->invokeProperty($providerWrapper, 'exclude', $exclude);

        $I->expectException(
            $exp,
            function () use ($helper, $providerWrapper, $exp) {
                $helper->invokeMethod($providerWrapper, 'checkRetry', [$exp, 0, ['test' => 'data']]);
            }
        );
    }

    /**
     * Проверить добавятся ли в базу недоставленные данные
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToCreateUndeliveredData(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Добавить недоставленные данные в базу');

        $data = ['test' => 'CreateUndeliveredData'];

        $providerWrapper = $this->streamer;
        $tableName       = 'test_table';
        $providerWrapper->setTable($tableName);

        $exp     = new ValidationException('тест');
        $exclude = [];

        $helper->invokeProperty($providerWrapper, 'exclude', $exclude);

        $I->expectException(
            $exp,
            function () use ($helper, $providerWrapper, $exp, $data) {
                $helper->invokeMethod($providerWrapper, 'checkRetry', [$exp, $this->attempt, $data]);
            }
        );

        $I->assertTrue(UndeliveredDataMock::$saved);
    }

    /**
     * Проверить не добавятся ли в базу недоставленные данные
     * если сработало исключение, но не превысилось количество попыток
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToNotCreateUndeliveredData(\UnitTester $I, \Helper\Unit $helper)
    {
        //Чтобы вернуть значение $saved в false
        UndeliveredDataMock::reload();
        $exclude = [
            ValidationException::class,
        ];

        $exp  = new ValidationException('тест');
        $data = ['test' => 'NotCreateUndeliveredData'];


        $providerWrapper = $this->streamer;
        $helper->invokeProperty($providerWrapper, 'exclude', $exclude);

        $I->expectException(
            $exp,
            function () use ($helper, $providerWrapper, $exp, $data) {
                $helper->invokeMethod($providerWrapper, 'checkRetry', [$exp, 0, $data]);
            }
        );

        $I->assertFalse(UndeliveredDataMock::$saved);
    }
}
