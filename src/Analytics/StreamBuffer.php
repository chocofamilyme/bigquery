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
     * Вероятность того что проверить буффе всех таблиц
     */
    const FORCE_PERCENT = 5;

    /**
     * @var BufferStructure
     */
    private $buffer;

    /**
     * @var null
     */
    private $callback = null;

    /**
     * DataBuffer constructor.
     *
     * @param callable $callback
     * @param int      $bufferSize
     */
    public function __construct(callable $callback, int $bufferSize)
    {
        $this->callback = $callback;
        $this->buffer   = new BufferStructure($bufferSize);
    }

    /**
     * @return array
     */
    private function getBufferData(): array
    {
        return $this->buffer->tables;
    }

    /**
     * @param string $tableName
     * @param array  $rows
     */
    public function addBuffer(string $tableName, array $rows): void
    {
        if (false == isset($this->buffer->tables[$tableName])) {
            $this->buffer->tables[$tableName] = [];
        }

        $this->buffer->tables[$tableName] = array_merge($this->buffer->tables[$tableName], $rows);
    }

    /**
     * @return int
     */
    private function size(): int
    {
        $size = 0;

        foreach ($this->buffer->tables as $table) {
            $size += count($table);
        }

        return $size;
    }

    /**
     */
    private function flush(): void
    {
        $this->buffer->tables    = [];
        $this->buffer->timestamp = time();
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return bool
     */
    public function run(ProviderInterface $provider): bool
    {
        if ($this->isOverLimit()) {
            $result = call_user_func_array($this->callback, [
                $provider,
                $this->getBufferData(),
            ]);

            $this->flush();

            return $result;
        }

        return false;
    }

    /**
     *
     * @return bool
     */
    private function isOverLimit(): bool
    {
        return $this->size() >= $this->buffer->maxSize or time() - self::TIME_LIMIT > $this->buffer->timestamp;
    }
}
