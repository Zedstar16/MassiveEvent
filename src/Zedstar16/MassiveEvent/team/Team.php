<?php

namespace Zedstar16\MassiveEvent\team;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\util\Utils;

class Team
{

    private int $team;

    private array $members = [];


    public function __construct(int $team){
        $this->team = $team;
    }

    public function getTeamName() : int{
        return EventManager::TEAM_NAMES[$this->team];
    }

    public function getTeamMembers() : array{
        return $this->members;
    }

    public function addMember(Player $player){
        $this->members[] = $player->getName();
    }

    public function removeMember(Player|String $player){
        $key = array_search(Utils::getUsername($player), $this->members);
        if(isset($this->members[$key])){
            unset($this->members[$key]);
        }
    }


}