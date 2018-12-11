<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Unit;

use Chocofamily\Analytics\BufferStructure;
use Chocofamily\Analytics\StreamBuffer;
use Helper\Analytics\ProviderMock;

class StreamBufferCest
{

    /**
     * @var StreamBuffer
     */
    private $streamBuffer;

    public function _before()
    {
        $this->streamBuffer = new StreamBuffer(function () {
            return true;
        }, 50);
    }

    /**
     * Проверить как правильно данные добавляются в буффер
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToAddBuffer(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Положить данные в буффер');

        $tables = [
            'test' => [
                [
                    ['id' => 1, 'data' => 'test 1'],
                ],
                [
                    ['id' => 2, 'data' => 'test 2'],
                    ['id' => 3, 'data' => 'test 3'],
                ],
            ],
        ];

        $this->streamBuffer->addBuffer('test', $tables['test'][0]);
        $this->streamBuffer->addBuffer('test', $tables['test'][1]);

        $actual = $helper->invokeMethod($this->streamBuffer, 'getBufferData');

        $expected['test'] = [
            $tables['test'][0][0],
            $tables['test'][1][0],
            $tables['test'][1][1],
        ];

        $I->assertEquals($expected, $actual);
    }

    /**
     * Проверить размер буффера
     *
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToSize(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Проверить размер буффера');

        $tables = [
            'test' => [
                [
                    ['id' => 1, 'data' => 'test 1'],
                ],
                [
                    ['id' => 2, 'data' => 'test 2'],
                    ['id' => 3, 'data' => 'test 3'],
                ],
            ],
        ];

        $this->streamBuffer->addBuffer('test', $tables['test'][0]);
        $this->streamBuffer->addBuffer('test', $tables['test'][1]);

        $actual = $helper->invokeMethod($this->streamBuffer, 'size');

        $I->assertEquals(3, $actual);
    }

    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToFlush(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Проверить очистку буфера');

        $this->streamBuffer->addBuffer('test', [['id' => 1, 'data' => 'test 1']]);

        $helper->invokeMethod($this->streamBuffer, 'flush');

        $actual = $helper->invokeMethod($this->streamBuffer, 'size');
        $I->assertEquals(0, $actual);
    }

    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToOverLimitMaxSize(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Проверить буфер на лимит размера');

        $streamBuffer = new StreamBuffer(function () {
            return true;
        }, 2);

        $streamBuffer->addBuffer('test', [['id' => 1, 'data' => 'test 1']]);
        $streamBuffer->addBuffer('test', [['id' => 2, 'data' => 'test 2']]);

        $actual = $helper->invokeMethod($streamBuffer, 'isOverLimit');

        $I->assertTrue($actual);
    }

    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToOverLimitTime(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Проверить буфер на лимит времени жихни');

        $this->streamBuffer->addBuffer('test', [['id' => 1, 'data' => 'test 1']]);

        $buffer            = new BufferStructure(50);
        $buffer->timestamp = time() - 1000;

        $helper->invokeProperty($this->streamBuffer, 'buffer', $buffer);

        $actual = $helper->invokeMethod($this->streamBuffer, 'isOverLimit');

        $I->assertTrue($actual);
    }


    /**
     * @param \UnitTester  $I
     * @param \Helper\Unit $helper
     *
     * @throws \ReflectionException
     */
    public function tryToRun(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantToTest('Проверить буфер на выполнение');

        $this->streamBuffer->addBuffer('test', [['id' => 1, 'data' => 'test 1']]);

        $buffer            = new BufferStructure(50);
        $buffer->timestamp = time() - 1000;

        $helper->invokeProperty($this->streamBuffer, 'buffer', $buffer);

        $provider = new ProviderMock();
        $actual   = $this->streamBuffer->run($provider);

        $I->assertTrue($actual);
    }
}
