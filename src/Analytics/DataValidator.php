<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Chocofamily\Analytics\Exceptions\ValidationException;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class Sender
 *
 * Проверяет данные на соответсвие схемы и очищает
 *
 * @package Chocofamily\Analytics
 */
class DataValidator implements ValidatorInterface
{

    private $clientData;

    private $badMessages = [];

    /**
     * Sender constructor.
     *
     * @param array $data
     *
     */
    public function __construct(array $data = [])
    {
        $this->clientData = $data;
    }

    /**
     * @return array
     * @throws ValidationException
     */
    public function check(): array
    {
        $this->validation();

        return $this->filter();
    }

    /**
     * @throws ValidationException
     */
    private function validation(): void
    {
        $validation = new Validation();
        $validation->add([
            'uuid',
        ], new PresenceOf([
            'message'      => 'The :field is required',
            'cancelOnFail' => true,
        ]));

        foreach ($this->clientData as $item) {
            $messages = $validation->validate($item);
            foreach ($messages as $massage) {
                if (empty($item['uuid'])) {
                    throw new ValidationException('The uuid is required');
                }

                $this->badMessages[$item['uuid']] = $massage;
            }
        }
    }

    /**
     * @return array
     */
    private function filter(): array
    {
        if (empty($this->badMessages)) {
            return $this->clientData;
        }

        return array_filter($this->clientData, function ($row) {
            return !isset($this->badMessages[$row['uuid']]);
        });
    }

    /**
     * @param $data
     */
    public function setClientData(array $data): void
    {
        $this->clientData = $data;
    }
}
