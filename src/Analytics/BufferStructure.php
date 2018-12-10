<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

/**
 * Class BufferStructure
 *
 * @package Chocofamily\Analytics
 */
class BufferStructure
{
    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var int
     */
    public $maxSize;

    /**
     * @var array
     */
    public $tables = [];

    public function __construct(int $maxSize)
    {
        $this->timestamp = time();
        $this->maxSize   = $maxSize;
    }
}
