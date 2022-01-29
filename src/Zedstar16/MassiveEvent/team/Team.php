<?php

namespace Zedstar16\MassiveEvent\team;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\util\Utils;

class Team
{

    private int $team_id;

    private array $members = [];

    public array $kills = [];
    public array $deaths = [];

    public function __construct(int $team_id)
    {
        $this->team_id = $team_id;
    }

    public function getTeamId() : int{
        return $this->team_id;
    }

    public function getTeamName(): int
    {
        return TeamHandler::TEAM_NAMES[$this->team_id];
    }

    public function getTeamMembers(): array
    {
        return $this->members;
    }

    public function addMember(Player $player)
    {
        $this->members[] = $player->getName();
    }

    public function removeMember(Player|string $player)
    {
        $key = array_search(Utils::getUsername($player), $this->members);
        if (isset($this->members[$key])) {
            unset($this->members[$key]);
        }
    }

    public function addKill($player){
        $username = Utils::getUsername($player);
        if(!isset($this->kills[$username])){
            $this->kills[$username] = 1;
        }else{
            $this->kills[$username]++;
        }
    }

    public function addDeath($player){
        $username = Utils::getUsername($player);
        if(!isset($this->deaths[$username])){
            $this->deaths[$username] = 1;
        }else{
            $this->deaths[$username]++;
        }
    }

    public function getRandomSpawnPosition(): Vector3
    {
        if(isset(Loader::$config["spawn_positions"][$this->team_id])) {
            $positions = Loader::$config["spawn_positions"][$this->team_id];
            $pos = $positions[mt_rand(0, count($positions) - 1)];
            return new Vector3($pos[0], $pos[1], $pos[2]);
        }else{
            // Just for testing purposes until we setup the actual coords so the code above can work
            return new Vector3(100,100,100);
        }
    }




}