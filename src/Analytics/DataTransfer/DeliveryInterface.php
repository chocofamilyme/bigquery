<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Analytics\DataTransfer;

use Chocofamily\Analytics\MapperInterface;

/**
 * Interface TransferInterface
 *
 * @package Chocofamily\Analytics\DataTransfer
 */
interface DeliveryInterface
{
    public function send();

    public function setTable(string $tableName): void;

    public function setMapper(MapperInterface $mapper): void;

    public function setClientData(array $data): void;

    public function clearErrors(): void;
}
