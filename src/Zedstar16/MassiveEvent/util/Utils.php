<?php

namespace Zedstar16\MassiveEvent\util;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
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
        if ($item->hasCustomName() or $item->hasEnchantments()) {
            if ($item->hasCustomName()) {
                $store[3] = str_replace("§", "&", $item->getCustomName());
            } else $store[3] = "DEFAULT";
            if ($item->hasEnchantments()) {
                foreach ($item->getEnchantments() as $enchantment)
                {
                    $store[] = $enchantment->getType()->getName();
                    $store[] = $enchantment->getLevel();
                }
            }
        }
        return implode(":", $store);
    }

    /**
     *  Create Item object from string representation
     *
     * @param string $string
     * @return Item
     */
    public static function itemFromString(string $string): Item
    {
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

    public static function timeToComponents(){
        $timings = array_flip(EventManager::TIMINGS);
        if(EventManager::$event_stage === EventManager::EVENT_INIT){
            $stage_end_time = $timings[EventManager::EVENT_IN_PROGRESS];
            $sum = $stage_end_time - Timer::getInstance()->getHeartbeat();
        }else{
            $stage_end_time = $timings[EventManager::EVENT_OVER];
            $sum = $stage_end_time - Timer::getInstance()->getHeartbeat();
        }
        $time = gmdate("i:s", $sum);
        return explode(":", $time);
    }

}