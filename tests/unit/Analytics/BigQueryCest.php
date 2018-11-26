<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\ProviderInterface;
use Chocofamily\Analytics\Providers\BigQuery;
use Chocofamily\Analytics\Exceptions\ValidationException;
use Helper\Analytics\Models\UndeliveredDataMock;

class BigQueryCest
{

    /** @var ProviderInterface */
    private $provider;

    public function setUp()
    {
        $analytics      = \Phalcon\Di::getDefault()->getShared('config')->analytics;
        $this->provider = new BigQuery($analytics);
    }

    /**
     * @param \UnitTester $I
     */
    public function tryToThrowTableIsNullException(\UnitTester $I)
    {
        $I->wantToTest('Правило на отсутсвие названия таблицы');
        $provider = $this->provider;
        $I->expectException(
            new ValidationException('Укажите таблицу'),
            function () use ($provider) {
                $provider->insert([]);
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

        $I->assertTrue($helper->invokeMethod($this->provider, 'checkRetry', [$exp, 1, ['test' => 'data']]));
    }

    /**
     * Проверить сработает исключение на превышения числа попыток
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToCheckRetryExceptionAttempt(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Выбросить исключение при привышении кол-ва попыток');

        $attempt  = 1;
        $tableName = 'test_table';
        $provider = $this->provider;
        $exp      = new ValidationException('тест');

        $helper->invokeProperty($provider, 'attempt', $attempt);
        $helper->invokeProperty($provider, 'tableName', $tableName);

        $I->expectException(
            $exp,
            function () use ($helper, $provider, $exp, $attempt) {
                $helper->invokeMethod($provider, 'checkRetry', [$exp, $attempt, ['test' => 'data']]);
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

        $provider = $this->provider;
        $helper->invokeProperty($provider, 'exclude', $exclude);

        $I->expectException(
            $exp,
            function () use ($helper, $provider, $exp) {
                $helper->invokeMethod($provider, 'checkRetry', [$exp, 0, ['test' => 'data']]);
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

        $attempt  = 1;
        $tableName = 'test_table';
        $data = ['test' => 'CreateUndeliveredData'];
        $provider = $this->provider;
        $exp      = new ValidationException('тест');
        $exclude = [];

        $helper->invokeProperty($provider, 'attempt', $attempt);
        $helper->invokeProperty($provider, 'tableName', $tableName);
        $helper->invokeProperty($provider, 'exclude', $exclude);

        $I->expectException(
            $exp,
            function () use ($helper, $provider, $exp, $data, $attempt) {
                $helper->invokeMethod($provider, 'checkRetry', [$exp, $attempt, $data]);
            }
        );

        $I->assertTrue(UndeliveredDataMock::$saved);
        //Чтобы вернуть значение $saved в false
        UndeliveredDataMock::reload();
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
        $exclude = [
            ValidationException::class,
        ];

        $exp = new ValidationException('тест');
        $tableName = 'test_table';
        $data = ['test' => 'NotCreateUndeliveredData'];


        $provider = $this->provider;
        $helper->invokeProperty($provider, 'exclude', $exclude);
        $helper->invokeProperty($provider, 'tableName', $tableName);

        $I->expectException(
            $exp,
            function () use ($helper, $provider, $exp, $data) {
                $helper->invokeMethod($provider, 'checkRetry', [$exp, 0, $data]);
            }
        );

        $I->assertFalse(UndeliveredDataMock::$saved);
    }
}
