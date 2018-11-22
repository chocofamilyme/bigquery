<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phalcon\Logger\AdapterInterface;
use Chocofamily\Analytics\Providers\BigQuery;

/**
 * Class Sender
 *
 * Отправляет данные в аналитику
 *
 * @package Chocofamily\Analytics
 */
class Sender extends Injectable
{

    /**
     * @var ProviderInterface
     */
    public $provider;

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
        $this->logger   = $this->getDI()->getShared('logger');
        $this->provider = new BigQuery($this->getDI()->getShared('config')->analytics);

        $this->validator = $validator;
        $this->mapper    = new NullMapper();
    }

    /**
     * @throws \Chocofamily\Analytics\Exceptions\ValidationException
     */
    public function send()
    {
        $rows = $this->validator->check();

        $this->dataMap($rows);
        $this->provider->insert(
            $this->prepare($rows)
        );

        $this->writeError();
    }


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
    private function writeError()
    {
        foreach ($this->provider->getErrors() as $key => $value) {
            $this->logger->warning($key.': '.$value);
        }
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
    private function dataMap(array &$rows)
    {
        foreach ($rows as &$row) {
            $this->mapper->process($row);
        }
    }
}
