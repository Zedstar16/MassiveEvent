<?php

namespace Zedstar16\MassiveEvent\listener;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\manager\Manager;

class EventListener implements Listener
{

    /**
     * @param EntityDamageEvent $event
     * @handleCancelled false
     */
    public function onEntityDamage(EntityDamageEvent $event)
    {
        $subject = $event->getEntity();
        if ($subject instanceof Player) {
            if (($subject->getHealth() - $event->getFinalDamage()) <= 0) {
                Manager::getInstance()->getSessionManager()->getSession($subject)->handleDeath();
                $event->cancel();
            }
        }
    }

    public function onInteract(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $healing_health = 8;

        if ($item->getId() == ItemIds::MUSHROOM_STEW) {
            if ($player->getHealth() < 20) {
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->setHealth($player->getHealth() + $healing_health);
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $sessionManager = Manager::getInstance()->getSessionManager();
        if($sessionManager->getSession($player) === null) {
            $sessionManager->addSession($player);
        }
    }



    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
     //   Manager::getInstance()->removeSession($player);
    }


}