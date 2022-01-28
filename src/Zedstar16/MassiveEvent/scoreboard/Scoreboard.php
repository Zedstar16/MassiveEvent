<?php

namespace Zedstar16\MassiveEvent\scoreboard;

use pocketmine\player\Player;

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
        switch ($scoreboard_stage){
            case self::SCOREBOARD_PRE_GAME:

                break;
        }
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