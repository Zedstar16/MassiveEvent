<?php

declare(strict_types=1);

namespace Zedstar16\MassiveEvent;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;
use Zedstar16\MassiveEvent\commands\KitCommand;
use Zedstar16\MassiveEvent\generator\VoidGenerator;
use Zedstar16\MassiveEvent\kit\Kit;
use Zedstar16\MassiveEvent\listener\EventListener;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\KitManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\manager\SessionManager;
use Zedstar16\MassiveEvent\tasks\async\AsyncOrderingTask;
use Zedstar16\MassiveEvent\team\TeamHandler;
use Zedstar16\MassiveEvent\timer\Timer;

class Loader extends PluginBase
{

    /** @var Loader|null */
    public static ?Loader $instance = null;
    public static array $config = [];

    public static array $client_data = [];

    protected function onLoad(): void
    {
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void", function (){});
    }

    protected function onEnable(): void
    {

        self::$instance = $this;
        $config_path = $this->getDataFolder() . "config.json";
        if (!file_exists($config_path)) {
            file_put_contents($config_path, "{}");
        }
        new Manager();
        self::$config = json_decode(file_get_contents($config_path), true);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $team_handler = new TeamHandler();
        $team_handler->createTeams();
         new Timer();
        $this->startTasks();

        $this->getServer()->getCommandMap()->register("kit", new KitCommand("kit", "Select a kit"));
    }

    public function startTasks(): void
    {
        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (): void {
            $team_handler = TeamHandler::getInstance();
            $data = [];
            for ($i = 0; $i < count(TeamHandler::TEAM_NAMES); $i++) {
                $team = $team_handler->getTeam($i);
                $data[] = [
                    "kills" => $team->kills,
                    "deaths" => $team->deaths
                ];
            }
            $json_string = json_encode($data);
            Server::getInstance()->getAsyncPool()->submitTask(new AsyncOrderingTask($json_string));
        }), 110, 20);

    }

}
