<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\Repeater;
use Chocofamily\Analytics\UndeliveredDataStorage;
use Chocofamily\Analytics\ValidatorInterface;

use Chocofamily\Analytics\Providers\BigQuery\Streamer as ProviderStreamer;
use Phalcon\Config;

/**
 * Class Sender
 *
 * Отправляет данные в аналитику
 *
 * @package Chocofamily\Analytics
 */
class Streamer extends Transfer
{

    const REPEAT_DELAY = 1;

    /**
     * @var Repeater
     */
    private $repeater;

    /**
     * @var array
     */
    private $excludeExceptions = [];

    /**
     * Streamer constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);

        /** @var \Phalcon\Config $config */
        $config                  = $this->getDI()->getShared('config')->analytics;
        $attempt                 = $config->get('repeater')->get('attempt', 5);
        $this->excludeExceptions = $config->get('repeater')->get('exclude', []);

        $this->transfer = new ProviderStreamer($config);

        $this->repeater = new Repeater(
            self::REPEAT_DELAY,
            $attempt,
            function ($exception) {
                return in_array(get_class($exception), $this->excludeExceptions);
            }
        );
    }

    /**
     * @throws \Chocofamily\Analytics\Exceptions\ClassNotFound
     * @throws \Chocofamily\Analytics\Exceptions\ValidationException
     */
    public function send()
    {
        /** @var Config $config */
        $config = $this->getDI()->getShared('config')->analytics;
        $rows   = $this->validator->check();

        $this->dataMap($rows);
        $this->transfer->setRows($this->prepare($rows));

        try {
            $this->repeater->run(function () {
                $this->transfer->execute();
            });
        } catch (\Exception $e) {
            if (false == in_array(get_class($e), $this->excludeExceptions)) {
                $undeliveredDataStorage = new UndeliveredDataStorage(
                    $config->get('undeliveredDataModel'),
                    $this->transfer->getTableName()
                );

                $undeliveredDataStorage->insert($rows);
            }
        }


        $this->writeError();
    }
}
