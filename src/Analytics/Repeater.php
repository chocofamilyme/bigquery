<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

/**
 * Class Repeater повторяет переданную функцию, если происходит не придвиденной исключение
 *
 * @package Chocofamily\Analytics
 */
class Repeater implements RepeaterInterface
{
    /**
     * @var int
     */
    private $delay = 0;

    /**
     * @var int
     */
    private $attempt = 0;

    /**
     * @var callable
     */
    private $excludeFunction;

    /**
     * Repeater constructor.
     *
     * @param int           $delay   задержка между повторами в микросекундах
     * @param int           $attempt количество повторов
     * @param callable|null $excludeFunction
     */
    public function __construct(int $delay, int $attempt, callable $excludeFunction = null)
    {
        $this->delay           = $delay;
        $this->attempt         = $attempt;
        $this->excludeFunction = $excludeFunction;
    }

    /**
     * @param callable $clientFunction
     * @param mixed    ...$arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function run(callable $clientFunction, ...$arguments)
    {
        $retryAttempt = 0;
        $exception    = null;
        while (true) {
            try {
                return call_user_func_array($clientFunction, $arguments);
            } catch (\Exception $exception) {
                if ($this->excludeFunction) {
                    if (call_user_func($this->excludeFunction, $exception)) {
                        throw $exception;
                    }
                }

                if ($retryAttempt >= $this->attempt) {
                    break;
                }

                $retryAttempt++;
                $this->calculateDelay($retryAttempt);
            }
        }

        throw $exception;
    }

    /**
     * @param int $retryAttempt
     */
    public function calculateDelay(int $retryAttempt): void
    {
        usleep($this->delay * $retryAttempt);
    }
}
