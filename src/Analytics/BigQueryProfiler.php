<?php
/**
 * @package Chocolife.me
 * @author  Kamet Aziza <kamet.a@chocolife.kz>
 */

namespace Chocofamily\Analytics;

use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;

/**
 * Class BigQueryProfiler
 *
 * @package Chocofamily\Analytics
 */
class BigQueryProfiler implements EventsAwareInterface
{
    /**
     * @var ManagerInterface
     */
    private $eventsManager;

    public function setEventsManager(ManagerInterface $eventsManager)
    {
        $this->eventsManager = $eventsManager;
    }

    public function getEventsManager(): ManagerInterface
    {
        return $this->eventsManager;
    }
}
