<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\MapperInterface;
use Chocofamily\Analytics\NullMapper;
use Chocofamily\Analytics\Providers\ProviderInterface;
use Chocofamily\Analytics\ValidatorInterface;
use Phalcon\Di\Injectable;
use Phalcon\Logger\AdapterInterface;

abstract class Transfer extends Injectable implements TransferInterface
{
    /**
     * @var ProviderInterface
     */
    public $transfer;

    /**
     * @var AdapterInterface
     */
    private $logger;

    /**
     * @var ValidatorInterface
     */
    public $validator;

    /** @var MapperInterface */
    private $mapper;

    /**
     * Sender constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->logger = $this->getDI()->getShared('logger');

        $this->validator = $validator;
        $this->mapper    = new NullMapper();
    }

    public function setTable(string $tableName): void
    {
        $this->transfer->setTable($tableName);
    }

    abstract public function send();

    /**
     * Подготавливает данные для провайдера
     * В параметрах должен быть insertId
     *
     * @param array $rows
     *
     * @return array
     */
    protected function prepare(array $rows): array
    {
        return array_map(function ($data) {
            return [
                'insertId' => $data['uuid'],
                'data'     => $data,
            ];
        }, $rows);
    }

    /**
     * Записать ошибки
     */
    protected function writeError(): void
    {
        foreach ($this->transfer->getErrors() as $key => $value) {
            $this->logger->warning($key.': '.$value);
        }
    }

    /**
     * Очищает буффер ошибок
     */
    public function clearErrors(): void
    {
        $this->transfer->clearErrors();
    }

    /**
     * @param MapperInterface $mapper
     */
    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    public function setClientData(array $data): void
    {
        $this->validator->setClientData($data);
    }

    /**
     * @param array $rows
     */
    protected function dataMap(array &$rows): void
    {
        foreach ($rows as &$row) {
            $this->mapper->process($row);
        }
    }
}
