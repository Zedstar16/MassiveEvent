<?php

namespace Zedstar16\MassiveEvent\scoreboard;

use pocketmine\player\Player;

class Scoreboard
{

    private Player $player;

    public function __construct(Player $player){
        $this->player = $player;
    }

}