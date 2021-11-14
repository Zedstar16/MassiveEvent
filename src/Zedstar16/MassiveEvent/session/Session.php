<?php

namespace Zedstar16\MassiveEvent\session;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\scoreboard\Scoreboard;

class Session
{
    private Player $player;

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function getScoreboard() : Scoreboard{
        // TODO
    }


}