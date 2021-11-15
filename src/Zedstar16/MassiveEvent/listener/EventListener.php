<?php

namespace Zedstar16\MassiveEvent\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemIds;

class EventListener implements Listener
{
    
    public function onInteract(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $healing_health = 8;

        if ($item->getId() == ItemIds::MUSHROOM_STEW) {
            if($player->getHealth() < 20) {
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->setHealth($player->getHealth() + $healing_health);
            }
        }
    }
    
}
