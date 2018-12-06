<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

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
    /**
     * Streamer constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);

        $this->transfer = new ProviderStreamer($this->getDI()->getShared('config')->analytics);
    }

    /**
     * @throws \Chocofamily\Analytics\Exceptions\ValidationException
     */
    public function send()
    {
        $rows = $this->validator->check();

        $this->dataMap($rows);
        $this->transfer->setRows($this->prepare($rows));
        $this->transfer->execute();

        $this->writeError();
    }
}
