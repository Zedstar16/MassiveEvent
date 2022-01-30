<?php

namespace Zedstar16\MassiveEvent\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemIds;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\team\TeamHandler;
use Zedstar16\MassiveEvent\util\Utils;

class ManagementCommand extends Command
{

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("pocketmine.group.operator");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if (Server::getInstance()->isOp($sender->getName())) {
            if (!isset($args[0])) {
                $sender->sendMessage("Invalid Arguments");
                return;
            }
            switch ($args[0]) {
                case "setkit":
                    if ($sender instanceof Player) {
                        $config_path = Loader::$instance->getDataFolder() . "kits.yml";
                        $data = yaml_parse_file($config_path);
                        $armor = [];
                        $inventory = [];
                        for ($i = 0; $i < 4; $i++) {
                            $item = $sender->getArmorInventory()->getItem($i);
                            if ($item->getId() !== ItemIds::AIR) {
                                $armor[$i] = Utils::itemToString($item);
                            }
                        }
                        foreach ($sender->getInventory()->getContents() as $index => $item) {
                            $inventory[$index] = Utils::itemToString($item);
                        }
                        $data[$args[1]] = [
                            "name" => $args[1],
                            "armor" => $armor,
                            "inventory" => $inventory
                        ];
                        yaml_emit_file($config_path, $data);
                        $sender->sendMessage("§aCreated kit §f$args[1] §afrom your current inventory contents");
                    }
                    break;

                case "addspawnpos":
                    if ($sender instanceof Player) {
                        $teamID = (int)$args[1];
                        $pos = $sender->getPosition();
                        Loader::$config["spawn_positions"][$teamID][] = [
                            (int)$pos->getFloorX(),
                            (int)$pos->getFloorY(),
                            (int)$pos->getFloorZ(),
                        ];
                        $teamname = TeamHandler::TEAM_NAMES[$teamID];
                        $sender->sendMessage("§aAdded a spawn pos for team §f$teamname §aat your position");

                    }
                    break;

                case "data":
                    if($args[1] === "teams"){
                        foreach (TeamHandler::getInstance()->getAllTeams() as $team){
                            var_dump($team->getTeamName());
                            print_r($team->kills);
                            print_r($team->deaths);
                            print_r($team->getTeamMembers());
                        }
                    }elseif($args[1] === "session"){
                        foreach (Manager::getInstance()->getSessionManager()->getAllSessions() as $sesh){
                            var_dump($sesh);
                        }
                    }
                    break;
            }
        }
    }
}