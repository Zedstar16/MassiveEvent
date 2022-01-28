<?php

namespace Zedstar16\MassiveEvent\session;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\scoreboard\ScoreFactory;
use Zedstar16\MassiveEvent\tasks\DeathTitleTask;
use Zedstar16\MassiveEvent\team\Team;
use Zedstar16\MassiveEvent\team\TeamHandler;

class Session
{
    private Player $player;


    public int $team_id;

    public function __construct(Player $player, $team_id){
        $this->player = $player;
        $this->team_id = $team_id;
    }



    public function updateScoreboard(){
        ScoreFactory::setScore($this->player, "");
    }

    public function handleDeath(){
        $this->player->setGamemode(GameMode::SPECTATOR());

        // Will teleport player to a slightly offset position to where they died to give a more spectator like feel
        $this->player->teleport($this->player->getPosition()->add(1, 2, 0));
        Loader::$instance->getScheduler()->scheduleRepeatingTask(new DeathTitleTask($this->player), 20);
    }

    public function getTeam() : Team {
        return TeamHandler::getInstance()->getTeam($this->team_id);
    }


}