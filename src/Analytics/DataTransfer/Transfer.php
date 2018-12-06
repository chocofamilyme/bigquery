<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\MapperInterface;
use Chocofamily\Analytics\NullMapper;
use Chocofamily\Analytics\Providers\ProviderInterface;
use Chocofamily\Analytics\Repeater;
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

    abstract public function send();

    /**
     * Подготавливает данные для провайдера
     * В параметрах должен быть insertId
     *
     * @param array $rows
     *
     * @return array
     */
    public function prepare(array $rows): array
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
    protected function writeError()
    {
        foreach ($this->transfer->getErrors() as $key => $value) {
            $this->logger->warning($key.': '.$value);
        }
    }

    /**
     * Очищает буффер ошибок
     */
    public function clearErrors()
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

    /**
     * @param array $rows
     */
    protected function dataMap(array &$rows)
    {
        foreach ($rows as &$row) {
            $this->mapper->process($row);
        }
    }
}
