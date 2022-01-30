<?php

namespace Zedstar16\MassiveEvent\scoreboard;

use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\Server;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\timer\Timer;
use Zedstar16\MassiveEvent\util\Utils;

class Scoreboard
{

    private Player $player;

    public const SCOREBOARD_PRE_GAME = 0;
    public const SCOREBOARD_IN_GAME = 1;
    public const SCOREBOARD_END_GAME = 2;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getScoreLines(int $scoreboard_stage) : array{
        $online = count(Server::getInstance()->getOnlinePlayers());
        $time = Timer::getInstance()->timeToComponents();
        $session = Manager::getInstance()->getSessionManager()->getSession($this->player);
        $lines = [
            "- §6§lGeneral",
            " §fOnline §7$online",
            "- §6§lEvent Info",
        ];
        switch ($scoreboard_stage){
            case self::SCOREBOARD_PRE_GAME:
                $lines = array_merge($lines, [
                    " §fStarting In §a$time[0]m $time[1]s",
                    " §bRun §6/info ",
                    " §bto view Event Info"
                ]);
                break;
            case self::SCOREBOARD_IN_GAME:
                $lines[] = " §fEnds In §a$time[0]m $time[1]s";
                $lines[] = "- §6§lYour Info";
                $lines[] = " §fTeam: ".$session->getTeam()->getTeamColor().$session->getTeam()->getTeamName();
                $lines[] = " §fKills: §a".$session->getKills();
                $lines[] = " §fDeaths: §a".$session->getDeaths();
                $deaths = $session->getDeaths() === 0 ? 1 : $session->getDeaths();
                $lines[] = " §fKDR: §6".round($session->getKills()/$deaths, 2);
                if(count(EventManager::$ordered_kills) >= 3){
                    if(!empty(EventManager::$leaderboard)) {
                       $lines = array_merge($lines, EventManager::$leaderboard);
                    }
                }
                break;
            case self::SCOREBOARD_END_GAME:
                $lines[] = " §fStatus: §cOver";
                $lines[] = "- §6§lYour Stats";
                $lines[] = " §fKills: §a".$session->getKills();
                $lines[] = " §fDeaths: §a".$session->getDeaths();
                $deaths = $session->getDeaths() === 0 ? 1 : $session->getDeaths();
                $lines[] = " §fKDR: §6".round($session->getKills()/$deaths, 2);
                if(count(EventManager::$ordered_kills) >= 3){
                    if(!empty(EventManager::$leaderboard)) {
                        $lines = array_merge($lines, EventManager::$leaderboard);
                    }
                }
                break;
        }
        return $this->format($lines);
    }

    private function format(array $lines) : array{
        foreach ($lines as $index => $line){
            $lines[$index] = "§e︱".$line;
        }
        return $lines;
    }


}