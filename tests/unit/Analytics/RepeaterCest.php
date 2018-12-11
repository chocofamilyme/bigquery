<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Repeater;

class RepeaterCest
{
    public function tryToPassArgumentsToClientFunction(\UnitTester $I)
    {
        $I->wantToTest('Правильная передача аргументов');

        $repeater = new Repeater(0, 0);
        $argument = 'response';

        $result = $repeater->run(function ($argument) {
            return $argument;
        }, $argument);

        $I->assertEquals($argument, $result);
    }

    public function tryToThrowException(\UnitTester $I)
    {
        $I->wantToTest('Выбросить исключение');

        $excludeExceptions = [
            ValidationException::class
        ];
        $message = 'error msg';
        $repeater = new Repeater(0, 0, function ($exception) use ($excludeExceptions) {
            return in_array(get_class($exception), $excludeExceptions);
        });

        $I->expectException(
            new ValidationException($message),
            function () use ($repeater, $message) {

                $repeater->run(function ($message) {
                    throw new ValidationException($message);
                }, $message);
            }
        );
    }

    public function tryToThrowErrorAfterRepeat(\UnitTester $I)
    {
        $I->wantToTest('Выбросить исключение после повторов');

        $message = 'error msg';
        $repeater = new Repeater(0, 0);

        $I->expectException(
            new ValidationException($message),
            function () use ($repeater, $message) {

                $repeater->run(function ($message) {
                    throw new ValidationException($message);
                }, $message);
            }
        );
    }
}
