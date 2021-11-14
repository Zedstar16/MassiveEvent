<?php

namespace Zedstar16\MassiveEvent\util;

use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\tasks\PlayerExecTask;

class Utils
{


    /**
     * Executes given callback for all players on server
     *
     * @param callable $callback
     * @param int $tick
     */
    public static function execAll(callable $callback, int $tick = 1){
        Loader::$instance->getScheduler()->scheduleRepeatingTask(new PlayerExecTask($callback), $tick);
    }

}