<?php

namespace Zedstar16\MassiveEvent\timer;

use pocketmine\scheduler\ClosureTask;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;

class Timer
{

    private static self $instance;

    private int $heartbeat;


    public function __construct()
    {
        $this->heartbeat = 0;
        self::$instance = $this;
        Loader::$instance->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (): void {
            Timer::getInstance()->update();
        }), 100, 20);
    }

    public function getHeartbeat(): int
    {
        return $this->heartbeat;
    }

    public function update()
    {
        $this->heartbeat++;
        Manager::getInstance()->getEventManager()->update($this->heartbeat);
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function timeToComponents()
    {
        $timings = array_flip(EventManager::TIMINGS);
        $stage_end_time = (EventManager::$event_stage === EventManager::EVENT_INIT)
            ? $timings[EventManager::EVENT_IN_PROGRESS]
            : $timings[EventManager::EVENT_OVER];

        $sum = $stage_end_time - $this->heartbeat;
        $time = gmdate("i:s", $sum);
        return explode(":", $time);
    }


}