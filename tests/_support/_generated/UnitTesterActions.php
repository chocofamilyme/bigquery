<?php  //[STAMP] 4e0082e968051c50bb25c9d77011e506
namespace _generated;

// This class was automatically generated by build task
// You should not change it manually as it will be overwritten on next build
// @codingStandardsIgnoreFile

trait UnitTesterActions
{
    /**
     * @return \Codeception\Scenario
     */
    abstract protected function getScenario();

    
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are equal. If you're comparing floating-point values,
     * you can specify the optional "delta" parameter which dictates how great of a precision
     * error are you willing to tolerate in order to consider the two values equal.
     *
     * Regular example:
     * ```php
     * <?php
     * $I->assertEquals($element->getChildrenCount(), 5);
     * ```
     *
     * Floating-point example:
     * ```php
     * <?php
     * $I->assertEquals($calculator->add(0.1, 0.2), 0.3, 'Calculator should add the two numbers correctly.', 0.01);
     * ```
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @param float  $delta
     * @see \Codeception\Module\Asserts::assertEquals()
     */
    public function assertEquals($expected, $actual, $message = null, $delta = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are not equal. If you're comparing floating-point values,
     * you can specify the optional "delta" parameter which dictates how great of a precision
     * error are you willing to tolerate in order to consider the two values not equal.
     *
     * Regular example:
     * ```php
     * <?php
     * $I->assertNotEquals($element->getChildrenCount(), 0);
     * ```
     *
     * Floating-point example:
     * ```php
     * <?php
     * $I->assertNotEquals($calculator->add(0.1, 0.2), 0.4, 'Calculator should add the two numbers correctly.', 0.01);
     * ```
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @param float  $delta
     * @see \Codeception\Module\Asserts::assertNotEquals()
     */
    public function assertNotEquals($expected, $actual, $message = null, $delta = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are same
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertSame()
     */
    public function assertSame($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertSame', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are not same
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotSame()
     */
    public function assertNotSame($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotSame', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is greater than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertGreaterThan()
     */
    public function assertGreaterThan($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertGreaterThan', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is greater or equal than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertGreaterThanOrEqual()
     */
    public function assertGreaterThanOrEqual($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertGreaterThanOrEqual', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is less than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertLessThan()
     */
    public function assertLessThan($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertLessThan', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is less or equal than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertLessThanOrEqual()
     */
    public function assertLessThanOrEqual($expected, $actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertLessThanOrEqual', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that haystack contains needle
     *
     * @param        $needle
     * @param        $haystack
     * @param string $message
     * @see \Codeception\Module\Asserts::assertContains()
     */
    public function assertContains($needle, $haystack, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertContains', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that haystack doesn't contain needle.
     *
     * @param        $needle
     * @param        $haystack
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotContains()
     */
    public function assertNotContains($needle, $haystack, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotContains', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that string match with pattern
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     * @see \Codeception\Module\Asserts::assertRegExp()
     */
    public function assertRegExp($pattern, $string, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertRegExp', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that string not match with pattern
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotRegExp()
     */
    public function assertNotRegExp($pattern, $string, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotRegExp', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a string starts with the given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @see \Codeception\Module\Asserts::assertStringStartsWith()
     */
    public function assertStringStartsWith($prefix, $string, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertStringStartsWith', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a string doesn't start with the given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @see \Codeception\Module\Asserts::assertStringStartsNotWith()
     */
    public function assertStringStartsNotWith($prefix, $string, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertStringStartsNotWith', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is empty.
     *
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertEmpty()
     */
    public function assertEmpty($actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertEmpty', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is not empty.
     *
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotEmpty()
     */
    public function assertNotEmpty($actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotEmpty', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is NULL
     *
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNull()
     */
    public function assertNull($actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNull', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is not NULL
     *
     * @param        $actual
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotNull()
     */
    public function assertNotNull($actual, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotNull', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that condition is positive.
     *
     * @param        $condition
     * @param string $message
     * @see \Codeception\Module\Asserts::assertTrue()
     */
    public function assertTrue($condition, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertTrue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the condition is NOT true (everything but true)
     *
     * @param        $condition
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotTrue()
     */
    public function assertNotTrue($condition, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotTrue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that condition is negative.
     *
     * @param        $condition
     * @param string $message
     * @see \Codeception\Module\Asserts::assertFalse()
     */
    public function assertFalse($condition, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertFalse', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the condition is NOT false (everything but false)
     *
     * @param        $condition
     * @param string $message
     * @see \Codeception\Module\Asserts::assertNotFalse()
     */
    public function assertNotFalse($condition, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotFalse', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file exists
     *
     * @param string $filename
     * @param string $message
     * @see \Codeception\Module\Asserts::assertFileExists()
     */
    public function assertFileExists($filename, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertFileExists', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file doesn't exist
     *
     * @param string $filename
     * @param string $message
     * @see \Codeception\Module\Asserts::assertFileNotExists()
     */
    public function assertFileNotExists($filename, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertFileNotExists', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expected
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertGreaterOrEquals()
     */
    public function assertGreaterOrEquals($expected, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertGreaterOrEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expected
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertLessOrEquals()
     */
    public function assertLessOrEquals($expected, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertLessOrEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertIsEmpty()
     */
    public function assertIsEmpty($actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertIsEmpty', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $key
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertArrayHasKey()
     */
    public function assertArrayHasKey($key, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertArrayHasKey', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $key
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertArrayNotHasKey()
     */
    public function assertArrayNotHasKey($key, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertArrayNotHasKey', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that array contains subset.
     *
     * @param array  $subset
     * @param array  $array
     * @param bool   $strict
     * @param string $message
     * @see \Codeception\Module\Asserts::assertArraySubset()
     */
    public function assertArraySubset($subset, $array, $strict = null, $message = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertArraySubset', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expectedCount
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertCount()
     */
    public function assertCount($expectedCount, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertCount', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $class
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertInstanceOf()
     */
    public function assertInstanceOf($class, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertInstanceOf', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $class
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertNotInstanceOf()
     */
    public function assertNotInstanceOf($class, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertNotInstanceOf', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $type
     * @param $actual
     * @param $description
     * @see \Codeception\Module\Asserts::assertInternalType()
     */
    public function assertInternalType($type, $actual, $description = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('assertInternalType', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Fails the test with message.
     *
     * @param $message
     * @see \Codeception\Module\Asserts::fail()
     */
    public function fail($message) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('fail', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Handles and checks exception called inside callback function.
     * Either exception class name or exception instance should be provided.
     *
     * ```php
     * <?php
     * $I->expectException(MyException::class, function() {
     *     $this->doSomethingBad();
     * });
     *
     * $I->expectException(new MyException(), function() {
     *     $this->doSomethingBad();
     * });
     * ```
     * If you want to check message or exception code, you can pass them with exception instance:
     * ```php
     * <?php
     * // will check that exception MyException is thrown with "Don't do bad things" message
     * $I->expectException(new MyException("Don't do bad things"), function() {
     *     $this->doSomethingBad();
     * });
     * ```
     *
     * @param $exception string or \Exception
     * @param $callback
     * @see \Codeception\Module\Asserts::expectException()
     */
    public function expectException($exception, $callback) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('expectException', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param       $object
     * @param       $methodName
     * @param array $parameters
     *
     * @return mixed
     * @throws \ReflectionException
     * @see \Helper\Unit::invokeMethod()
     */
    public function invokeMethod($object, $methodName, $parameters = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('invokeMethod', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $object
     * @param $propertyName
     *
     * @throws \ReflectionException
     * @see \Helper\Unit::invokeProperty()
     */
    public function invokeProperty($object, $propertyName) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('invokeProperty', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Sends message to exchange by sending exchange name, message
     * and (optionally) a routing key
     *
     * ``` php
     * <?php
     * $I->pushToExchange('exchange.emails', 'thanks');
     * $I->pushToExchange('exchange.emails', new AMQPMessage('Thanks!'));
     * $I->pushToExchange('exchange.emails', new AMQPMessage('Thanks!'), 'severity');
     * ?>
     * ```
     *
     * @param string $exchange
     * @param string|\PhpAmqpLib\Message\AMQPMessage $message
     * @param string $routing_key
     * @see \Codeception\Module\AMQP::pushToExchange()
     */
    public function pushToExchange($exchange, $message, $routing_key = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('pushToExchange', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Sends message to queue
     *
     * ``` php
     * <?php
     * $I->pushToQueue('queue.jobs', 'create user');
     * $I->pushToQueue('queue.jobs', new AMQPMessage('create'));
     * ?>
     * ```
     *
     * @param string $queue
     * @param string|\PhpAmqpLib\Message\AMQPMessage $message
     * @see \Codeception\Module\AMQP::pushToQueue()
     */
    public function pushToQueue($queue, $message) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('pushToQueue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Declares an exchange
     *
     * This is an alias of method `exchange_declare` of `PhpAmqpLib\Channel\AMQPChannel`.
     *
     * ```php
     * <?php
     * $I->declareExchange(
     *     'nameOfMyExchange', // exchange name
     *     'topic' // exchange type
     * )
     * ```
     *
     * @param string $exchange
     * @param string $type
     * @param bool $passive
     * @param bool $durable
     * @param bool $auto_delete
     * @param bool $internal
     * @param bool $nowait
     * @param array $arguments
     * @param int $ticket
     * @return mixed|null
     * @see \Codeception\Module\AMQP::declareExchange()
     */
    public function declareExchange($exchange, $type, $passive = null, $durable = null, $auto_delete = null, $internal = null, $nowait = null, $arguments = null, $ticket = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('declareExchange', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Declares queue, creates if needed
     *
     * This is an alias of method `queue_declare` of `PhpAmqpLib\Channel\AMQPChannel`.
     *
     * ```php
     * <?php
     * $I->declareQueue(
     *     'nameOfMyQueue', // exchange name
     * )
     * ```
     *
     * @param string $queue
     * @param bool $passive
     * @param bool $durable
     * @param bool $exclusive
     * @param bool $auto_delete
     * @param bool $nowait
     * @param array $arguments
     * @param int $ticket
     * @return mixed|null
     * @see \Codeception\Module\AMQP::declareQueue()
     */
    public function declareQueue($queue = null, $passive = null, $durable = null, $exclusive = null, $auto_delete = null, $nowait = null, $arguments = null, $ticket = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('declareQueue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Binds a queue to an exchange
     *
     * This is an alias of method `queue_bind` of `PhpAmqpLib\Channel\AMQPChannel`.
     *
     * ```php
     * <?php
     * $I->bindQueueToExchange(
     *     'nameOfMyQueueToBind', // name of the queue
     *     'transactionTracking.transaction', // exchange name to bind to
     *     'your.routing.key' // Optionally, provide a binding key
     * )
     * ```
     *
     * @param string $queue
     * @param string $exchange
     * @param string $routing_key
     * @param bool $nowait
     * @param array $arguments
     * @param int $ticket
     * @return mixed|null
     * @see \Codeception\Module\AMQP::bindQueueToExchange()
     */
    public function bindQueueToExchange($queue, $exchange, $routing_key = null, $nowait = null, $arguments = null, $ticket = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('bindQueueToExchange', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if message containing text received.
     *
     * **This method drops message from queue**
     * **This method will wait for message. If none is sent the script will stuck**.
     *
     * ``` php
     * <?php
     * $I->pushToQueue('queue.emails', 'Hello, davert');
     * $I->seeMessageInQueueContainsText('queue.emails','davert');
     * ?>
     * ```
     *
     * @param string $queue
     * @param string $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\AMQP::seeMessageInQueueContainsText()
     */
    public function canSeeMessageInQueueContainsText($queue, $text) {
        return $this->getScenario()->runStep(new \Codeception\Step\ConditionalAssertion('seeMessageInQueueContainsText', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if message containing text received.
     *
     * **This method drops message from queue**
     * **This method will wait for message. If none is sent the script will stuck**.
     *
     * ``` php
     * <?php
     * $I->pushToQueue('queue.emails', 'Hello, davert');
     * $I->seeMessageInQueueContainsText('queue.emails','davert');
     * ?>
     * ```
     *
     * @param string $queue
     * @param string $text
     * @see \Codeception\Module\AMQP::seeMessageInQueueContainsText()
     */
    public function seeMessageInQueueContainsText($queue, $text) {
        return $this->getScenario()->runStep(new \Codeception\Step\Assertion('seeMessageInQueueContainsText', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Takes last message from queue.
     *
     * ``` php
     * <?php
     * $message = $I->grabMessageFromQueue('queue.emails');
     * ?>
     * ```
     *
     * @param string $queue
     * @return \PhpAmqpLib\Message\AMQPMessage
     * @see \Codeception\Module\AMQP::grabMessageFromQueue()
     */
    public function grabMessageFromQueue($queue) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('grabMessageFromQueue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Purge a specific queue defined in config.
     *
     * ``` php
     * <?php
     * $I->purgeQueue('queue.emails');
     * ?>
     * ```
     *
     * @param string $queueName
     * @see \Codeception\Module\AMQP::purgeQueue()
     */
    public function purgeQueue($queueName = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('purgeQueue', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Purge all queues defined in config.
     *
     * ``` php
     * <?php
     * $I->purgeAllQueues();
     * ?>
     * ```
     * @see \Codeception\Module\AMQP::purgeAllQueues()
     */
    public function purgeAllQueues() {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('purgeAllQueues', func_get_args()));
    }
}
