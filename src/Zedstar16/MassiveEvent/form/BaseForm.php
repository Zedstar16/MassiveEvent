<?php

namespace Zedstar16\MassiveEvent\form;

use pocketmine\player\Player;

abstract class BaseForm
{

    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->sendForm();
    }

    abstract public function sendForm();

}