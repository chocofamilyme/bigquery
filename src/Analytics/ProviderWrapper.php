<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Services\UndeliveredData;

class ProviderWrapper
{
    const MAX_DELAY_MICROSECONDS = 60000000;

    private $provider;

    /**
     * @var int
     */
    private $attempt = 5;

    /**
     * @var array
     */
    private $exclude = [];

    private $undeliveredDataModel;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
        $config = $provider->getConfig();

        $this->attempt = $config['repeater']->get('attempt', 5);
        $this->undeliveredDataModel = $config['undeliveredDataModel'];
        $this->exclude = $config['repeater']->get('exclude')->toArray();
    }

    public function insert(array $rows)
    {
        if (empty($this->provider->getTable())) {
            throw new ValidationException('Укажите таблицу');
        }

        $retryAttempt   = 0;
        $insertResponse = null;
        while (true) {
            try {
                $insertResponse = $this->provider->insert($rows);
                break;
            } catch (\Exception $exception) {
                $this->checkRetry($exception, ++$retryAttempt, $rows);
                usleep($this->calculateDelay());
            }
        }

        if (!$insertResponse) {
            return false;
        }

        if (false == $insertResponse->isSuccessful()) {
            $this->collectErrors($insertResponse->failedRows());

            return false;
        }

        return $insertResponse->isSuccessful();
    }

    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Calculates exponential delay.
     *
     * @return int
     */
    private function calculateDelay()
    {
        return min(
            mt_rand(0, 1000000) + (pow(2, $this->attempt) * 1000000),
            self::MAX_DELAY_MICROSECONDS
        );
    }

    /**
     * @param $errors
     */
    private function collectErrors($errors)
    {
        foreach ($errors as $row) {
            $message = '';
            foreach ($row['errors'] as $error) {
                $message .= $error['reason'].': '.$error['message'].PHP_EOL;
            }
            $this->provider->addErrors($row['rowData']['uuid'], $message);
        }
    }

    /**
     * @param \Exception $exception
     * @param int        $attempt
     * @param array      $rows
     *
     * @return bool
     * @throws \Exception
     */
    private function checkRetry(\Exception $exception, int $attempt, array $rows)
    {
        $attemptsExceeded = $this->attemptsExceeded($attempt);
        if (in_array(get_class($exception), $this->exclude) or $attemptsExceeded) {
            if ($attemptsExceeded) {
                $this->createUndeliveredData($rows);
            }
            throw $exception;
        }

        return true;
    }

    /**
     * @param $attempt
     *
     * @return bool
     */
    private function attemptsExceeded($attempt)
    {
        return $attempt >= $this->attempt;
    }

    /**
     * @param array $rows
     *
     * @throws ValidationException
     * @throws \Chocofamily\Analytics\Exceptions\ClassNotFound
     */
    private function createUndeliveredData(array $rows)
    {
        if ($this->undeliveredDataModel === null) {
            throw new ValidationException(
                'Укажите модель для записи недоставленных данных в analytics, по ключу `undeliveredDataModel`'
            );
        }
        $undeliveredService = new UndeliveredData($this->undeliveredDataModel);
        $tableName          = $this->provider->getTableName();
        $data               = \json_encode($rows);
        $undeliveredService->create($tableName, $data);
    }
}
