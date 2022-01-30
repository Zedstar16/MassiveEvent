<?php

namespace Zedstar16\MassiveEvent\util;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\Server;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\tasks\PlayerExecTask;
use Zedstar16\MassiveEvent\timer\Timer;

class Utils
{


    /**
     * Executes given callback for all players on server
     *
     * @param callable $callback
     * @param int $tick
     */
    public static function execAll(callable $callback, int $tick = 1){
        Loader::$instance->getScheduler()->scheduleRepeatingTask(new PlayerExecTask($callback), $tick);
    }

    /**
     * Easily get the username of player from a given variable without needing to check type
     *
     * @param Player|String $player
     * @return String|null
     */
    public static function getUsername(Player|String $player) : ?String {
        if(is_string($player)){
            return $player;
        }
        if($player instanceof Player){
            return $player->getName();
        }
        return null;
    }


    /**
     *  Convert an Item to a string representation storable in config file
     *
     * @param Item $item
     * @return string
     */
    public static function itemToString(Item $item){

        $store = [];
        $store[] = $item->getId();
        $store[] = $item->getMeta();
        $store[] = $item->getCount();
       // $item->jsonSerialize();
        if ($item->hasCustomName() or $item->hasEnchantments()) {
            if ($item->hasCustomName()) {
                $store[3] = str_replace("§", "&", $item->getCustomName());
            } else $store[3] = "DEFAULT";
            if ($item->hasEnchantments()) {
                foreach ($item->getEnchantments() as $enchantment)
                {
                   // var_dump($enchantment->getType()->getName()->getText());
                    $ench_name = Server::getInstance()->getLanguage()->translate($enchantment->getType()->getName());
                    $store[] =  $ench_name;
                    $store[] = $enchantment->getLevel();
                }
            }
        }
    //    var_dump($store);
        return implode(":", $store);
     //   return json_encode($item->jsonSerialize());
    }

    /**
     *  Create Item object from string representation
     *
     * @param string $string
     * @return Item
     */
    public static function itemFromString(string $string): Item
    {
       // return Item::jsonDeserialize(json_decode($string, true));

        $data = explode(":", $string);
        $id = (int)$data[0];
        $damage = (int)$data[1];
        $count = (int)$data[2];
        $item = ItemFactory::getInstance()->get($id, $damage, $count);
        if (isset($data[3])) {
            if ($data[3] !== "DEFAULT") {
                $item->setCustomName(str_replace("&", "§", $data[3]));
            }
            if (isset($data[4])) {
                $data = array_slice($data, 4);
                foreach ($data as $i => $iValue) {
                    if (is_int($i / 2)) {
                        $enchant =   StringToEnchantmentParser::getInstance()->parse($iValue);
                        $instance = new EnchantmentInstance($enchant, $data[$i + 1]);
                        $item->addEnchantment($instance);
                        $i++;
                    } else $i++;
                }
                return $item;
            } else return $item;
        } else return $item;

    }

    public static function translateInputMethod(int $data) : string{
        $inputs = [
            1 => "Mouse",
            2 => "Touch",
            3 => "Controller",
            4 => "Motion Controller"
        ];
        return $inputs[$data] ?? "Unknown";
    }

    public static function formatHealthString(float $health){
        $health = (int)$health;
        $color = "§a";
        if ($health < 16 && $health >= 12) {
            $color = "§e";
        } elseif ($health < 12 && $health >= 8) {
            $color = "§6";
        } elseif ($health < 8 && $health >= 4) {
            $color = "§c";
        } elseif ($health < 4 && $health >= 0) {
            $color = "§4";
        }
        return "{$color}{$health}❤";
    }

    public static function formatLeaderboardPlacement(string $player, int $kills){
        $sessionMgr = Manager::getInstance()->getSessionManager()->getSession($player);
        if($sessionMgr !== null) {
            return $sessionMgr->getTeam()->getTeamColor() . $player . " §f - §b" . (string)$kills;
        }else return "";
    }


}