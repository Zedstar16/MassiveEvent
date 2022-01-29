<?php

namespace Zedstar16\MassiveEvent\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\form\KitSelectionForm;

class KitCommand extends Command
{

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            new KitSelectionForm($sender);
        }
    }
}