<?php

namespace Zedstar16\MassiveEvent\manager;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\team\Team;

class EventManager
{

    /*
     * Class solely for managing event related data/functions
     */


    public static $event_stage = -1;

    public const EVENT_INIT = 0;
    public const EVENT_IN_PROGRESS = 1;
    public const EVENT_OVER = 2;

    public static $ordered_kills = [];
    public static $ordered_kdr = [];

    public const TIMINGS = [
        0 => self::EVENT_INIT,
        5 * 60 => self::EVENT_IN_PROGRESS,
        50 * 60 => self::EVENT_OVER
    ];



    public function update(int $heartbeat)
    {

    }

    public function handleKill(Player $killer, Player $victim)
    {
        $sessionMgr = Manager::getInstance()->getSessionManager();
        $killer_team = $sessionMgr->getSession($killer)->getTeam();
        $victim_team = $sessionMgr->getSession($victim)->getTeam();
        $killer_team->addKill($killer);
        $victim_team->addDeath($victim);
    }


}