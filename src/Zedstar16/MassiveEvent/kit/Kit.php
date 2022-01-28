<?php

namespace Zedstar16\MassiveEvent\kit;

use pocketmine\item\Item;
use pocketmine\player\Player;

class Kit
{

    private string $name;
    private array $armor_contents;
    private array $inventory_contents;

    public function __construct(String $kitName, array $armor_contents, array $inventory_contents)
    {
        $this->name = $kitName;
        $this->armor_contents = $armor_contents;
        $this->inventory_contents = $inventory_contents;
    }

    public function setKit(Player $player){
        foreach ($this->armor_contents as $armorSlot => $item){
            $player->getArmorInventory()->setItem($armorSlot, $item);
        }
        foreach ($this->inventory_contents as $inventorySlot => $item){
            $player->getInventory()->setItem($inventorySlot, $item);
        }
    }

    public function getName() : String{
        return $this->name;
    }

}