<?php

namespace Zedstar16\MassiveEvent\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class EventListener implements Listener
{
    public function onInteract(PlayerItemUseEvent $event)
    {

        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getId() == 282) {
            if($player->getHealth() == 20){
                
            } else {
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->setHealth($player->getHealth() + 8);
            }
        }
    }
}