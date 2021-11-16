<?php

namespace Zedstar16\MassiveEvent\team;

use pocketmine\player\Player;

class TeamHandler
{

    public const TEAM_NAMES = ["Red", "Yellow", "Green", "Blue"];

    /** @var Team[] */
    private array $teams = [];

    private array $player_data = [];


    private static TeamHandler $instance;

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance() : TeamHandler{
        return self::$instance;
    }

    public function createTeams()
    {
        foreach (self::TEAM_NAMES as $team_id => $TEAM_NAME) {
            $this->teams[] = new Team($team_id);
        }
    }

    public function getTeam($team_id): Team
    {
        return $this->teams[$team_id];
    }


    /**
     * Simple algorithm designed to figure out which team has the least players.
     * Team with the least players will have the unassigned player given to them
     *
     * @param Player $player
     * @return Team
     */
    public function assignTeam(Player $player) : Team
    {
        $team_with_least_members = [-1, 999];
        foreach ($this->teams as $team) {
            $memberCount = count($team->getTeamMembers());
            if ($memberCount <= $team_with_least_members[1]) {
                $team_with_least_members[$team->getTeamId()] = $memberCount;
            }
        }
        $chosen_team = $this->teams[$team_with_least_members[0]];
        $chosen_team->addMember($player);

        // The purpose of saving the team the player is assigned is so that if they relog
        // they will be assigned the same team they started off with
        $this->player_data[$player->getName()] = $chosen_team->getTeamId();
        return $chosen_team;
    }

    public function hasTeamAssigned(Player $player)
    {
        return isset($this->player_data[$player->getName()]);
    }

    public function getAssignedTeam(Player $player){
        return $this->player_data[$player->getName()];
    }


}