<?php

namespace Zedstar16\MassiveEvent\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\lib\jojoe77777\FormAPI\SimpleForm;

class InfoCommand extends Command
{

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {

        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $form = new SimpleForm(function ($player, $data) {
            });
            $form->setTitle("§l§4Event Information");
            $contents = [
            "§4- §fWheres my rank??? This is an event server, everything will return back to normal after the event ends",
            "§4- §fThis event is basically a team vs teams conflict",
            "§4- §fYou have been assigned to a §6random§f team",
            "§4- §fYou can select a kit to use via §6/kit",
            "§4- §fThe objective is to kill as many members of other teams as possible",
            "§4- §fYou will not be able to damage your team members",
            "§4- §fYou may not team with members of other teams or farm kills     ",
            "§4- §fAll normal ownage rules apply during this event",
            ];
            $form->setContent(implode("\n", $contents));
            $form->addButton("Ok");
            $sender->sendForm($form);
        }
    }
}