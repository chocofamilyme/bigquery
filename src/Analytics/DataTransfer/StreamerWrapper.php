<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\StreamBuffer;
use Chocofamily\Analytics\UndeliveredDataStorage;
use Chocofamily\Analytics\ValidatorInterface;

use Chocofamily\Analytics\Providers\BigQuery\Streamer as ProviderStreamer;

/**
 * Class Sender
 *
 * Отправляет данные в аналитику через выбранного провайдера
 *
 * @package Chocofamily\Analytics
 */
class StreamerWrapper extends Delivery
{

    private $buffer;

    /**
     * Streamer constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);

        $this->transfer = new ProviderStreamer($this->config);
        $this->buffer   = new StreamBuffer($this->getStreamFunction());
    }

    /**
     */
    public function send()
    {
        $rows = $this->validator->check();

        $this->dataMap($rows);

        $rows = $this->prepare($rows);

        $this->buffer->addBuffer($this->transfer->getTableName(), $rows);

        if ($this->buffer->run($this->transfer)) {
            $this->writeError();
        }
    }

    /**
     * @return \Closure
     */
    private function getStreamFunction()
    {
        return function (ProviderStreamer $transfer, array $rows) {
            $transfer->setRows($rows);
            try {
                return $transfer->execute();
            } catch (\Exception $e) {
                if (false == $this->isExclude($e)) {
                    $undeliveredDataStorage = new UndeliveredDataStorage(
                        $this->config->get('undeliveredDataModel'),
                        $this->transfer->getTableName()
                    );

                    $undeliveredDataStorage->insert($rows);
                }

                throw $e;
            }
        };
    }
}
