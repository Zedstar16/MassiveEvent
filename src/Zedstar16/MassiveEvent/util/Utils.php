<?php

namespace Zedstar16\MassiveEvent\util;

use pocketmine\player\Player;
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

    /**
     * Easily get the username of player from a given variable without needing to check type
     *
     * @param Player|String $player
     * @return String|null
     */
    public static function getUsername(Player|String $player) : ?String {
        if(is_string($player)){
            return $player;
        }
        if($player instanceof Player){
            return $player->getName();
        }
        return null;
    }

}