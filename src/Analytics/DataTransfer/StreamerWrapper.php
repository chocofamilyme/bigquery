<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\StreamBuffer;
use Chocofamily\Analytics\StreamBufferInterface;
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

    /**
     * @var StreamBufferInterface
     */
    private $buffer;

    /**
     * Streamer constructor.
     *
     * @param ValidatorInterface $validator
     * @param int                $bufferSize
     */
    public function __construct(ValidatorInterface $validator, int $bufferSize)
    {
        parent::__construct($validator);

        $this->transfer = new ProviderStreamer($this->config);
        $this->buffer   = new StreamBuffer($this->getStreamFunction(), $bufferSize);
    }

    /**
     * Отправить данные
     *
     * @throws ValidationException
     */
    public function send()
    {
        if(empty($this->transfer->getTableName())) {
            throw new ValidationException('Укажите таблицу');
        }

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
        return function (ProviderStreamer $transfer, array $tables) {
            $exception = null;

            foreach ($tables as $key => $rows) {
                try {
                    $transfer->setTable($key);
                    $transfer->setRows($rows);
                    $this->execute();
                } catch (\Exception $e) {
                    if (false == $this->isExclude($e)) {
                        $undeliveredDataStorage = new UndeliveredDataStorage(
                            $this->config->get('undeliveredDataModel'),
                            $this->transfer->getTableName()
                        );

                        $undeliveredDataStorage->insert($rows);
                    }

                    $exception = $e;
                }
            }

            if ($exception) {
                throw $exception;
            }
        };
    }
}
