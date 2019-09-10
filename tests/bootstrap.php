<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */
require __DIR__.'/../vendor/autoload.php';

use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();

$di->set(
    'logger',
    function () {
        return new \Phalcon\Logger\Adapter\Stream('php://stderr');
    }
);

$di->set(
    'config',
    function () {
        return new \Phalcon\Config([
            'analytics' => [
                'dataset'       => 'holding',
                'queueName'     => 'analytics',
                'exchangeType'  => 'direct',

                'undeliveredDataModel' => \Helper\Analytics\Models\UndeliveredDataMock::class,
                'connection' => [
                    'keyFilePath' => __DIR__.'/_data/keys/key.json',
                    //'keyFile'     => {},
                ],
                'mappers' => [
                    'tableName' => \Chocofamily\Analytics\NullMapper::class,
                ],

                'repeater'    => [
                    'attempt' => 5,
                    'exclude' => [
                        \InvalidArgumentException::class,
                        \Google\Cloud\Core\Exception\NotFoundException::class,
                    ],
                ],

                'pathStorage' => __DIR__.'/storage',
            ],
        ]);
    }
);

return new Application($di);
