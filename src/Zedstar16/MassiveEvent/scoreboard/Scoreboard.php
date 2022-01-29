<?php

namespace Zedstar16\MassiveEvent\scoreboard;

use pocketmine\player\Player;
use pocketmine\Server;
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
        $lines = [
            "- §6§lGeneral",
            " §fOnline §7$online",
            "- §6§lEvent Info",
        ];
        switch ($scoreboard_stage){
            case self::SCOREBOARD_PRE_GAME:
                $lines = array_merge($lines, [
                    "§e︱ §fStarting In §a$time[0]m $time[1]s",
                    "§e︱ §bOpen the book ",
                    "§e︱ §bto view Event Info"
                ]);
                break;
            case self::SCOREBOARD_IN_GAME:

                break;
            case self::SCOREBOARD_END_GAME:
                $lines = array_merge($lines, [
                    " §fStatus: §cOver",
                ]);
                break;
        }
        return $lines;
    }

    private function format(array $lines) : array{
        foreach ($lines as $index => $line){
            $lines[$index] = "§e︱".$line;
        }
        return $lines;
    }

    private function preGameScorelines($values)
    {
        return [
            ""
        ];
    }

    private function inGameScorelines($values)
    {
        return [
            ""
        ];
    }

    private function endGameScorelines($values)
    {
        return [
            ""
        ];
    }

}