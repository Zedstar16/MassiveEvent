<?php

namespace Zedstar16\MassiveEvent\manager;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\session\Session;
use Zedstar16\MassiveEvent\team\TeamHandler;

class Manager
{

    /*
     * Class that manages and handles all aspects of plugin
     */

    private static Manager $instance;
    /** @var Session[] */
    private array $sessions;

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance(): Manager
    {
        return self::$instance;
    }

    public function addSession(Player $player)
    {
        $team_handler = TeamHandler::getInstance();

        $team = $team_handler->hasTeamAssigned($player) ? $team_handler->getAssignedTeam($player) : $team_handler->assignTeam($player);
        $session = new Session($player, $team->getTeamId());

        // Key as player name for easy access to retrieving a session
        $this->sessions[$player->getName()] = $session;
    }

    public function getSession(Player $player): Session
    {
        return $this->sessions[$player->getName()];
    }

    public function removeSession(Player $player)
    {
        $username = $player->getName();
        if (isset($this->sessions[$username])) {
            unset($this->sessions[$username]);
        }
    }


}