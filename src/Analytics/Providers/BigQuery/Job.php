<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\Providers\BigQuery;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Google\Cloud\Core\ExponentialBackoff;

class Job extends Transfer
{
    /**
     * @var string
     */
    private $file = '';

    public function setFile(string $fileName)
    {
        $this->file = $fileName;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function execute(): bool
    {
        if (empty($this->getTable())) {
            throw new ValidationException('Укажите таблицу');
        }

        $loadJobConfig = $this->getTable()
            ->load(fopen($this->file, 'r'))
            ->ignoreUnknownValues(self::DEFAULT_OPTIONS['ignoreUnknownValues'])
            ->sourceFormat('NEWLINE_DELIMITED_JSON');

        $job = $this->getTable()->runJob($loadJobConfig);

        $backoff = new ExponentialBackoff($this->attempt);

        $backoff->execute(function () use ($job) {
            $job->reload();
            if (!$job->isComplete()) {
                $this->addErrors(0, 'Job has not yet completed');
            }
        });

        if (isset($job->info()['status']['errorResult'])) {
            $reason = $job->info()['status']['errorResult']['reason'];
            $error  = $job->info()['status']['errorResult']['message'];
            $this->addErrors(1, $reason.': '.$error);
        }

        return $job->isComplete();
    }
}