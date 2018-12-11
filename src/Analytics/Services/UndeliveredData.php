<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics\Services;

use Chocofamily\Analytics\Exceptions\ClassNotFound;
use Phalcon\Mvc\Model;

/**
 * Class UndeliveredData обрабатывает не отправленные данные
 *
 * @package Chocofamily\Analytics\Services
 */
class UndeliveredData
{
    const STATUS_FAILED     = 0;
    const STATUS_SUCCESSFUL = 1;

    /** @var Model */
    private $model;

    /**
     * UndeliveredData constructor.
     *
     * @param string $modelClass
     *
     * @throws ClassNotFound
     */
    public function __construct(string $modelClass)
    {
        if (!class_exists($modelClass)) {
            throw new ClassNotFound('Класс '.$modelClass.' не найден');
        }
        $this->model = new $modelClass;
    }

    /**
     * @param string $tableName
     * @param string $data
     * @param int    $status
     *
     * @return Model
     */
    public function create(string $tableName, string $data, int $status = self::STATUS_FAILED)
    {
        $this->model->table_name = $tableName;
        $this->model->data       = $data;
        $this->model->status     = $status;

        //TODO throw exception on error?
        $this->model->save();
        $this->model->refresh();

        return $this->model;
    }

    public function findAllUndelivered($limit = 100)
    {
        return $this->model::query()
            ->where('status=:status:')
            ->bind(['status' => self::STATUS_FAILED])
            ->limit($limit)
            ->execute();
    }
}
