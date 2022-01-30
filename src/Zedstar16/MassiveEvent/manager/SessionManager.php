<?php

namespace Zedstar16\MassiveEvent\manager;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\session\Session;
use Zedstar16\MassiveEvent\team\TeamHandler;
use Zedstar16\MassiveEvent\util\Utils;

class SessionManager
{
    /*
     * Class that manages and handles player sessions
     */

    /** @var Session[] */
    private array $sessions = [];


    public function addSession(Player $player)
    {
        $team_handler = TeamHandler::getInstance();

        $team = $team_handler->hasTeamAssigned($player) ? $team_handler->getAssignedTeam($player) : $team_handler->assignTeam($player);
        $session = new Session($player, $team->getTeamId());

        // Key as player name for easy access to retrieving a session
        $this->sessions[$player->getName()] = $session;
    }

    public function getAllSessions(): array
    {
        return $this->sessions;
    }

    /**
     * @param Player|String $player
     * @return Session|null
     */
    public function getSession(Player|String $player): ?Session
    {
        $player = Utils::getUsername($player);
        return $this->sessions[$player] ?? null;
    }


    public function removeSession(Player $player)
    {
        $username = $player->getName();
        if (isset($this->sessions[$username])) {
            unset($this->sessions[$username]);
        }
    }
}