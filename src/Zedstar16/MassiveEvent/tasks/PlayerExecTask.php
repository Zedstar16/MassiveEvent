<?php

namespace Zedstar16\MassiveEvent\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class PlayerExecTask extends Task
{

    /** @var callable */
    public $callback;
    /** @var Player[] */
    public array $players = [];

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p;
        }
    }


    public function onRun(): void
    {
        // This task will execute a callback for each player on the server
        // The callback is executed for one player per tick in order to prevent server freezes by running a foreach for 100-200 players

        if (empty($this->players) || !isset($this->players[0])) {
            $this->getHandler()->cancel();
            $this->players = [];
            return;
        }
        $callback = $this->callback;
        $callback($this->players[0]);
        unset($this->players[0]);
        $players = $this->players;
        $this->players = [];
        if (count($players) > 0) {
            foreach ($players as $player) {
                $this->players[] = $player;
            }
        }
    }

}