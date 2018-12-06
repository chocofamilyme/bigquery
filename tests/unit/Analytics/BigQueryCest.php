<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\ProviderInterface;
use Chocofamily\Analytics\Providers\BigQuery;
use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\ProviderWrapper;
use Helper\Analytics\Models\UndeliveredDataMock;

class BigQueryCest
{

    /** @var ProviderWrapper */
    private $providerWrapper;

    private $attempt = 1;

    /** @var ProviderWrapper */
    public function setUp(\Helper\Unit $helper)
    {
        $tableName = 'test_table';
        $analytics      = \Phalcon\Di::getDefault()->getShared('config')->analytics;
        $provider = new BigQuery($analytics);
        $helper->invokeProperty($provider, 'tableName', $tableName);

        $this->providerWrapper = new ProviderWrapper($provider);
        $helper->invokeProperty($this->providerWrapper, 'attempt', $this->attempt);
    }

    /**
     * @param \UnitTester $I
     */
    public function tryToThrowTableIsNullException(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Правило на отсутсвие названия таблицы');

        $providerWrapper = $this->providerWrapper;

        $I->expectException(
            new ValidationException('Укажите таблицу'),
            function () use ($providerWrapper) {
                $providerWrapper->insert([]);
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

        $I->assertTrue($helper->invokeMethod($this->providerWrapper, 'checkRetry', [$exp, 0, ['test' => 'data']]));
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

        $providerWrapper = $this->providerWrapper;
        $exp      = new ValidationException('тест');


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

        $providerWrapper = $this->providerWrapper;
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
        $providerWrapper = $this->providerWrapper;
        $exp      = new ValidationException('тест');
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

        $exp = new ValidationException('тест');
        $data = ['test' => 'NotCreateUndeliveredData'];


        $providerWrapper = $this->providerWrapper;
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
