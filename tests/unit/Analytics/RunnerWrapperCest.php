<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\DataTransfer\RunnerWrapper;
use Chocofamily\Analytics\DataValidator;

class RunnerWrapperCest
{

    /**
     * @var RunnerWrapper
     */
    private $runner;

    public function _before()
    {
        $pathStorage = \Phalcon\Di::getDefault()->getShared('config')->analytics->get('pathStorage');

        if (false == is_dir($pathStorage)) {
            mkdir($pathStorage);
        }

        $data         = [
            [
                'uuid'       => '1',
                'created_at' => '2015-08-13 12:00:00',
                'info'       => 'test',
            ],
        ];
        $validator    = new DataValidator($data);
        $this->runner = new RunnerWrapper($validator);
    }

    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     * @throws \Chocofamily\Analytics\Exceptions\NotFoundFileException
     */
    public function tryToCreateTempFile(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Создание пути для файла');

        $helper->invokeMethod($this->runner, 'createTempFile');

        $tempFile = $this->runner->getFilePath();
        $I->assertNotEmpty($tempFile);
    }

    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     * @throws \Chocofamily\Analytics\Exceptions\NotFoundFileException
     */
    public function tryToWriteTempFile(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Запись в файл');

        $helper->invokeMethod($this->runner, 'createTempFile');

        $helper->invokeMethod($this->runner, 'writeTempFile', [['test' => 'test']]);

        $tempFile = $this->runner->getFilePath();
        $I->assertTrue(is_file($tempFile));
    }
}
