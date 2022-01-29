<?php

namespace Zedstar16\MassiveEvent\kit;

use pocketmine\item\Item;
use pocketmine\player\Player;

class Kit
{

    private string $name;
    /** @var Item[]  */
    private array $armor_contents;
    /** @var Item[]  */
    private array $inventory_contents;

    public function __construct(String $kitName, array $armor_contents, array $inventory_contents)
    {
        $this->name = $kitName;
        $this->armor_contents = $armor_contents;
        $this->inventory_contents = $inventory_contents;
    }

    public function setKit(Player $player){

        // Clear absolutely every inventory the player has, because players can be very sneaky sometimes
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();
        $player->getCraftingGrid()->clearAll();

        foreach ($this->armor_contents as $armorSlot => $item){
            $player->getArmorInventory()->setItem($armorSlot, $item);
        }
        foreach ($this->inventory_contents as $inventorySlot => $item){
            $player->getInventory()->setItem($inventorySlot, $item);
        }
    }

    /**
     * @return Item[]
     */
    public function getArmorContents() : array{
        return $this->armor_contents;
    }

    /**
     * @return Item[]
     */
    public function getInventoryContents() : array{
        return $this->inventory_contents;
    }

    public function getName() : String{
        return $this->name;
    }

    /**
     *  Returns a string with the kit's items listed in a suitable way to display in a form
     *
     * @return string
     */
    public function getStringContentsList() : string{
        $contents = "§aKit Contents\n\n§eArmor:";
        $slot_names = ["Helmet", "Chestplate", "Leggings", "Boots"];
        $ench_abbreviations = [
            "Protection" => "Prot",
            "Unbreaking" => "Unbr",
            "Sharpness" => "Sharp"
        ];
        foreach ($this->armor_contents as $slot => $armorContent) {
            $contents .= "§e" . $slot_names[$slot] . ": §b" . $armorContent->getVanillaName();
            foreach ($armorContent->getEnchantments() as $enchantment) {
                $ench_name = $enchantment->getType()->getName();
                $contents .= " " . ($ench_abbreviations[$ench_name] ?? $ench_name) . " " . $enchantment->getLevel() . ",";
            }
        }
        $item_list = [];
        $item_map = [];
        foreach ($this->inventory_contents as $item) {
            $name = $item->getName();
            $item_map[$name] = $item;
            if (!isset($item_list[$name])) {
                $item_list[$name] = 1;
            } else {
                $item_list[$name]++;
            }
        }
        foreach ($item_list as $itemName => $count) {
            $item = $item_map[$itemName];
            $contents .= "§e" . $count . "x " . $item->getVanillaName();
            foreach ($item->getEnchantments() as $enchantment) {
                $ench_name = $enchantment->getType()->getName();
                $contents .= " " . ($ench_abbreviations[$ench_name] ?? $ench_name) . " " . $enchantment->getLevel() . ",";
            }
        }
        return $contents;
    }

}