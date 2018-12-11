<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Helper\Analytics\Models;

class UndeliveredDataMock extends \Phalcon\Mvc\Model
{
    public static $saved;
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $table_name;

    /**
     * @var string
     */
    public $data;

    /**
     * @var integer
     */
    public $status;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function save($data = null, $whiteList = null)
    {
        if ($this->table_name !== null) {
            self::$saved = true;
        }
    }

    public function update($data = null, $whiteList = null)
    {
    }

    public function refresh()
    {
    }

    public static function reload()
    {
        self::$saved = false;
    }
}
