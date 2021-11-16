<?php

namespace Zedstar16\MassiveEvent\tasks;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use Zedstar16\MassiveEvent\manager\Manager;

class DeathTitleTask extends Task
{

    private int $tick = 0;
    private Player $player;

    private const RESPAWN_TIME = 5;

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function onRun(): void
    {
        if($this->tick === self::RESPAWN_TIME){
            $this->player->sendTitle("§aRespawning...");
            $position = Manager::getInstance()->getSession($this->player)->getTeam()->getRandomSpawnPosition();
            $this->player->teleport($position);
            $this->player->setGamemode(GameMode::ADVENTURE());
            $this->getHandler()->cancel();
            return;
        }
        $seconds = 5 - $this->tick;
        $this->player->sendTitle("§cYou Died", "§7Respawning in §a{$seconds} §7seconds");
        $this->tick++;
    }

}