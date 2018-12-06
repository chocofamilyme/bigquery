<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Chocofamily\Analytics\Providers\BigQuery\Job;
use Chocofamily\Analytics\ValidatorInterface;

/**
 * Для запуска задач
 */
class Runner extends Transfer
{

    const FOLDER        = 'analytics';
    const TEMP_FILE_EXT = 'json';

    /**
     * @var string
     */
    private $pathStorage = '';

    /**
     * @var string
     */
    private $tempFile = '';

    /**
     * Runner constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);

        $this->transfer = new Job($this->getDI()->getShared('config')->analytics);

        $this->pathStorage = $this->getDI()->getShared('config')->analytics->get('pathStorage');
    }


    /**
     * @throws ValidationException
     */
    public function send()
    {
        $rows = $this->validator->check();

        $this->dataMap($rows);
        $this->writeTempFile($rows);

        try {
            $this->transfer->setFile($this->getFilePath());
            $this->transfer->execute();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->deleteTempFile();
        }

        $this->writeError();
    }

    /**
     * @return string
     * @throws ValidationException
     */
    private function getFilePath(): string
    {
        if ($this->tempFile) {
            return $this->tempFile;
        }

        $this->createTempFile();

        return $this->tempFile;
    }

    /**
     * @throws ValidationException
     */
    private function createTempFile()
    {
        if (empty($this->pathStorage) and false == is_dir($this->pathStorage)) {
            throw new ValidationException(
                sprintf('Папка для хранения временных файлов не существует, проверти настройки параметра pathStorage: %s',
                    $this->pathStorage));
        }

        $fullPath = rtrim($this->pathStorage, '\\/').DIRECTORY_SEPARATOR.self::FOLDER;
        if (false == is_dir($fullPath)) {
            mkdir($fullPath);
        }

        $hash = rand(1000, 9999).time().getmypid();

        $this->tempFile = $fullPath.DIRECTORY_SEPARATOR.md5($hash).'.'.self::TEMP_FILE_EXT;
    }

    /**
     * Записать данные в файл
     *
     * @param array $rows
     *
     * @throws ValidationException
     */
    private function writeTempFile(array $rows)
    {
        $fp = fopen($this->getFilePath(), 'w');
        foreach ($rows as $row) {
            fwrite($fp, \json_encode($row, JSON_UNESCAPED_UNICODE).PHP_EOL);
        }

        fclose($fp);
    }

    /**
     * @throws ValidationException
     */
    private function deleteTempFile()
    {
        unlink($this->getFilePath());
    }
}
