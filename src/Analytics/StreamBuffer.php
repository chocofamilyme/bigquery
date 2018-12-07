<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Chocofamily\Analytics\Providers\ProviderInterface;

/**
 * Class DataBuffer накапливает данные таблиц перед отправкой, чтобы отправить пачкой при превышении литима
 * времени жизни или размера буфера
 *
 * С вероятностью установленной в константе FORCE_PERCENT проверяет на литмит буффер всех таблиц
 *
 * @package Chocofamily\Analytics
 */
class StreamBuffer implements StreamBufferInterface
{

    /**
     * Сколько данные должны лежать в буфере (секунды)
     */
    const TIME_LIMIT = 600;

    /**
     * Размер буффера по умолчанию
     */
    const SIZE_LIMIT_DEFAULT = 50;

    /**
     * Вероятность того что проверить буффе всех таблиц
     */
    const FORCE_PERCENT = 5;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var null
     */
    private $callback = null;

    /**
     * DataBuffer constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param string $tableName
     *
     * @return array
     */
    private function getBufferData(string $tableName): array
    {
        if (false == isset($this->buffer[$tableName])) {
            return [];
        }

        return $this->buffer[$tableName]['data'];
    }

    /**
     * @param string $tableName
     * @param int    $sizeLimit
     * @param array  $row
     */
    public function addBuffer(string $tableName, array $row, int $sizeLimit = self::SIZE_LIMIT_DEFAULT): void
    {
        if (false == isset($this->buffer[$tableName])) {
            $this->buffer[$tableName]['timestamp'] = time();
            $this->buffer[$tableName]['sizeLimit'] = $sizeLimit;
        }

        $this->buffer[$tableName]['data'] = array_merge($this->buffer[$tableName]['data'], $row);
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    private function size(string $tableName): int
    {
        if (false == isset($this->buffer[$tableName])) {
            return 0;
        }

        return count($this->buffer[$tableName]['data']);
    }

    /**
     * @param string $tableName
     */
    private function flush(string $tableName): void
    {
        if (isset($this->buffer[$tableName])) {
            unset($this->buffer[$tableName]);
        }
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return bool
     */
    public function run(ProviderInterface $provider): bool
    {
        $tableName = $provider->getTableName();
        if ($this->isOverLimit($tableName)) {
            $result = call_user_func_array($this->callback, [
                $provider,
                $this->getBufferData($tableName),
            ]);

            $this->flush($tableName);
            $this->force($provider);

            return $result;
        }

        return false;
    }

    /**
     * @param string $tableName
     *
     * @return bool
     */
    private function isOverLimit(string $tableName): bool
    {
        if (false == isset($this->buffer[$tableName])) {
            return false;
        }

        return $this->size($tableName) >= $this->buffer[$tableName]['sizeLimit'] or
            time() - self::TIME_LIMIT > $this->buffer[$tableName]['timestamp'];
    }

    /**
     * @param ProviderInterface $provider
     */
    private function force(ProviderInterface $provider)
    {
        if (rand(1, 100) < self::FORCE_PERCENT) {
            foreach ($this->buffer as $tableName => $buffer) {
                $provider->setTable($tableName);
                $this->run($provider);
            }
        }
    }
}
