<?php

declare(strict_types=1);

namespace Zedstar16\MassiveEvent;

use pocketmine\plugin\PluginBase;
use Zedstar16\MassiveEvent\listener\EventListener;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\team\TeamHandler;

class Loader extends PluginBase{

    /** @var Loader|null */
    public static ?Loader $instance = null;
    public static array $config = [];

    protected function onEnable(): void
    {
        self::$instance = $this;
        $config_path = $this->getDataFolder()."config.json";
        if(!file_exists($config_path)){
            file_put_contents($config_path, "{}");
        }
        self::$config = json_decode(file_get_contents($config_path), true);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $team_handler = new TeamHandler();
        $team_handler->createTeams();
        new Manager();
    }

}
