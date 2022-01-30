<?php

namespace Zedstar16\MassiveEvent\manager;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use Zedstar16\MassiveEvent\team\Team;
use Zedstar16\MassiveEvent\util\Utils;

class EventManager
{

    /*
     * Class solely for managing event related data/functions
     */


    public static $event_stage = 0;

    public const EVENT_INIT = 0;
    public const EVENT_IN_PROGRESS = 1;
    public const EVENT_OVER = 2;

    public static $ordered_kills = [];
    public static $ordered_kdr = [];

    public static $leaderboard = [];

    public const TIMINGS = [
        self::EVENT_INIT => 0,
        self::EVENT_IN_PROGRESS => 3 * 60,
        self::EVENT_OVER => 9 * 60
    ];


    public function updateLeaderboard()
    {
        if (count(self::$ordered_kills) >= 3) {
            $lines = [];
            $keys = array_keys(self::$ordered_kills);
            $lines[] = "- §6§lLeaderboard:";
            $lines[] = " §l§e#1§r - " . Utils::formatLeaderboardPlacement($keys[0], self::$ordered_kills[$keys[0]]);
            $lines[] = " §l§f#2§r - " . Utils::formatLeaderboardPlacement($keys[1], self::$ordered_kills[$keys[1]]);
            $lines[] = " §l§6#3§r - " . Utils::formatLeaderboardPlacement($keys[2], self::$ordered_kills[$keys[2]]);

            $kdr_keys = array_keys(self::$ordered_kdr);
            $lines[] = " §l§e#1 KDR§r - " . Utils::formatLeaderboardPlacement($kdr_keys[0], self::$ordered_kdr[$kdr_keys[0]]);
            self::$leaderboard = $lines;
        }
    }


    public function update(int $heartbeat)
    {
        if (self::$event_stage >= self::EVENT_IN_PROGRESS) {
            $this->updateLeaderboard();
        }

        if ($heartbeat === self::TIMINGS[self::EVENT_IN_PROGRESS]){
            self::$event_stage = self::EVENT_IN_PROGRESS;
            Utils::execAll(function ($player){
                /** @var Player $player */
                $player->sendTitle("§aEvent Starting...");
                $session = Manager::getInstance()->getSessionManager()->getSession($player);
                if($session !== null) {
                    $pos = $session->getTeam()->getRandomSpawnPosition();
                    $session->getSelectedKit()->setKit($player);
                    $player->teleport(new Position($pos->getX(), $pos->getY(), $pos->getZ(), Server::getInstance()->getWorldManager()->getWorldByName("kit1")));
                }
            });
        }
        if($heartbeat === self::TIMINGS[self::EVENT_OVER]){
            self::$event_stage = self::EVENT_OVER;
            foreach (Server::getInstance()->getOnlinePlayers() as $player){
                $player->sendTitle("§6Event Over", "§bThank you for participating");
            }
        }
    }

    public function handleKill(Player $killer, Player $victim)
    {
        Server::getInstance()->broadcastMessage("§4⚔ §c{$killer->getName()} §7stabbed §c{$victim->getName()}");
        $sessionMgr = Manager::getInstance()->getSessionManager();
        $killer_team = $sessionMgr->getSession($killer)->getTeam();
        $victim_team = $sessionMgr->getSession($victim)->getTeam();
        $killer_team->addKill($killer);
        $victim_team->addDeath($victim);
    }


}