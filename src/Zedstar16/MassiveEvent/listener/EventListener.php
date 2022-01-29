<?php

namespace Zedstar16\MassiveEvent\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\Manager;

class EventListener implements Listener
{


    /**
     * @param EntityDamageByEntityEvent $event
     * @handleCancelled false
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event)
    {
        $killer = $event->getDamager();
        $victim = $event->getEntity();
        if ($killer instanceof Player && $victim instanceof Player) {
            if (($victim->getHealth() - $event->getFinalDamage()) <= 0) {
                Manager::getInstance()->getSessionManager()->getSession($victim)->handleDeath();
                Manager::getInstance()->getEventManager()->handleKill($killer, $victim);
                $event->cancel();
            }
        }
    }

    public function onChat(PlayerChatEvent $event){
        $p = $event->getPlayer();
        if(Server::getInstance()->isOp($p->getName())){
            $event->setFormat("§b> §a{$p->getName()}§8: §b{$event->getMessage()}");
        }else {
            $event->setFormat("§b⤕ {$p->getName()}§8: §f{$event->getMessage()}");
        }
        $check = Manager::getInstance()->getChatHandler()->processMessage($p, $event->getMessage());
        if(!$check[0]){
            $p->sendMessage($check[1]);
            $event->cancel();
        }
    }

    public function onCommandPreProcess(PlayerCommandPreprocessEvent $event){
        $p = $event->getPlayer();
        $check = Manager::getInstance()->getChatHandler()->processMessage($p, $event->getMessage());
        if(!$check[0]){
            $p->sendMessage($check[1]);
            $event->cancel();
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
        $event->setJoinMessage("§8[§a⇛§8] §a".$event->getPlayer()->getName());
        $sessionManager = Manager::getInstance()->getSessionManager();
        if($sessionManager->getSession($player) === null) {
            $sessionManager->addSession($player);
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $event->setQuitMessage("§8[§4⭅§8] §4".$event->getPlayer()->getName());
    }

    public function onLogin(DataPacketReceiveEvent $event){
        $pk = $event->getPacket();
        if($pk instanceof LoginPacket){
            // TODO client data related stuff
            print_r($pk->clientDataJwt);
        }
    }


}