<?php

namespace Zedstar16\MassiveEvent\listener;

use pocketmine\block\Chest;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;

class EventListener implements Listener
{


    /**
     * @param EntityDamageByEntityEvent $event
     * @handleCancelled false
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event)
    {
        try {
            $killer = $event->getDamager();
            $victim = $event->getEntity();
            if ($killer instanceof Player && $victim instanceof Player) {
                if ($killer->getPosition()->distance($victim->getPosition()) > 5) {
                    $event->cancel();
                }
                $victim_session = Manager::getInstance()->getSessionManager()->getSession($victim);
                $killer_session = Manager::getInstance()->getSessionManager()->getSession($killer);
                if ($victim_session->getTeam()->getTeamId() === $killer_session->getTeam()->getTeamId()) {
                    $event->cancel();
                }
                if (($victim->getHealth() - $event->getFinalDamage()) <= 0) {
                    $victim_session = Manager::getInstance()->getSessionManager()->getSession($victim);
                    $victim_session->handleDeath();
                    Manager::getInstance()->getEventManager()->handleKill($killer, $victim);
                    $killer_session = Manager::getInstance()->getSessionManager()->getSession($killer);
                    $killer_session->getSelectedKit()->setKit($killer);
                    $event->cancel();
                }
            }
        }catch (\Throwable $err){}
    }

    public function onEntityDamage(EntityDamageEvent $event)
    {
        $p = $event->getEntity();
        if ($p instanceof Player) {
            if (EventManager::$event_stage === EventManager::EVENT_INIT) {
                $event->cancel();
            }
            $session = Manager::getInstance()->getSessionManager()->getSession($p);
            if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
                $p->teleport($session->getTeam()->getRandomSpawnPosition());
                $event->cancel();
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                $event->cancel();
            }
            try {
                $session->updateScoreTag();
            }catch (\Throwable $error){}
        }
    }

    public function onRegainHealth(EntityRegainHealthEvent $event)
    {
        $p = $event->getEntity();
        if ($p instanceof Player) {
            $session = Manager::getInstance()->getSessionManager()->getSession($p);
            try {
                $session->updateScoreTag();
            }catch (\Throwable $error){}
        }
    }


    public function onChat(PlayerChatEvent $event)
    {
        $p = $event->getPlayer();
        if (Server::getInstance()->isOp($p->getName())) {
            $event->setFormat("§b> §a{$p->getName()}§8: §b{$event->getMessage()}");
        } else {
            $color = Manager::getInstance()->getSessionManager()->getSession($p)->getTeam()->getTeamColor();
            $event->setFormat("§b⤕ $color{$p->getName()}§8: §f{$event->getMessage()}");
        }
        if(!Server::getInstance()->isOp($p->getName())) {
            $check = Manager::getInstance()->getChatHandler()->processMessage($p, $event->getMessage());
            if (!$check[0]) {
                $p->sendMessage($check[1]);
                $event->cancel();
            }
        }
    }

    public function oncmd(CommandEvent $event)
    {
        $p = $event->getSender();
        $cmd = explode(" ", $event->getCommand())[0];
        if ($cmd !== "kit" && $cmd !== "info") {
            if ($p instanceof Player && !Server::getInstance()->isOp($p->getName())) {
                $check = Manager::getInstance()->getChatHandler()->processMessage($p, $event->getCommand());
                if (!$check[0]) {
                    $p->sendMessage($check[1]);
                    $event->cancel();
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if (!Server::getInstance()->isOp($event->getPlayer()->getName())) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        if (!Server::getInstance()->isOp($event->getPlayer()->getName())) {
            $event->cancel();
        }
    }

    public function onDrop(PlayerDropItemEvent $event)
    {
        $event->cancel();
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $event->setDrops([]);
        $event->setDeathMessage("§7⟴ §c{$event->getPlayer()->getName()} unexpectedly died");
    }

    public function onItemUse(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        $session = Manager::getInstance()->getSessionManager()->getSession($player);

        if ($item->getId() == ItemIds::MUSHROOM_STEW) {
            if ($player->getHealth() < 20) {
                if ($session->getInputMethod() === "Controller") {
                    for ($i = -8; $i <= 0; $i++) {
                        $index = abs($i);
                        if ($player->getInventory()->getItem($index)->getId() === ItemIds::MUSHROOM_STEW) {
                            $player->getInventory()->setItem($index, VanillaItems::AIR());
                        }
                    }
                } else {
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 2, 3));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 1, 1));
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $block = $event->getBlock();
        if ($block instanceof Chest) {
            $event->cancel();
        }
    }


    public function onSpawn(PlayerRespawnEvent $event)
    {
        $p = $event->getPlayer();
        $sessionManager = Manager::getInstance()->getSessionManager();
        if (EventManager::$event_stage === EventManager::EVENT_IN_PROGRESS) {
            $p->teleport($sessionManager->getSession($p)->getTeam()->getRandomSpawnPosition());
        }

    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $event->setJoinMessage("§8[§a⇛§8] §a" . $event->getPlayer()->getName());
        if (EventManager::$event_stage === EventManager::EVENT_INIT) {
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("WaitingZone")->getSpawnLocation());
        }
        $sessionManager = Manager::getInstance()->getSessionManager();
        if ($sessionManager->getSession($player) === null) {
            $sessionManager->addSession($player);
            if (EventManager::$event_stage === EventManager::EVENT_IN_PROGRESS) {
                $player->sendMessage("§6Looks like you've joined the event mid-way, run §b/info §6to get more information for what this is all about\n§6This is simply an event server and kitpvp will return to normal after its over!");
                $player->teleport($sessionManager->getSession($player)->getTeam()->getRandomSpawnPosition());
            }
        }
        $player->setDisplayName($sessionManager->getSession($player)->getTeam()->getTeamColor() . $player->getName());

    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $sessionManager = Manager::getInstance()->getSessionManager();
        $sessionManager->removeSession($event->getPlayer());
        $event->setQuitMessage("§8[§4⭅§8] §4" . $event->getPlayer()->getName());
    }

    public function onExhaust(PlayerExhaustEvent $event){
        $event->cancel();
    }


}