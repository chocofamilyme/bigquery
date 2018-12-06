<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\Repeater;
use Chocofamily\Analytics\ValidatorInterface;

use Chocofamily\Analytics\Providers\BigQuery\Streamer as ProviderStreamer;

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
        $excludeExceptions       = $this->excludeExceptions;

        $this->transfer = new ProviderStreamer($config);

        $this->repeater = new Repeater(
            self::REPEAT_DELAY,
            $attempt,
            function ($exception) use ($excludeExceptions) {
                return in_array(get_class($exception), $excludeExceptions);
            }
        );
    }

    /**
     */
    public function send()
    {
        $rows = $this->validator->check();

        $this->dataMap($rows);
        $this->transfer->setRows($this->prepare($rows));

        try {
            $this->repeater->run(function (ProviderStreamer $transfer) {
                $transfer->execute();
            }, $this->transfer);
        } catch (\Exception $e) {
            if (false == in_array(get_class($e), $this->excludeExceptions)) {
                //TODO SAVE
            }
        }


        $this->writeError();
    }
}
