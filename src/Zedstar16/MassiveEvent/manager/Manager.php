<?php

namespace Zedstar16\MassiveEvent\manager;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\session\Session;
use Zedstar16\MassiveEvent\team\TeamHandler;

class Manager
{

    /*
     * Class that manages and handles all aspects of plugin
     */

    private static Manager $instance;

    private SessionManager $sessionManager;
    private KitManager $kitManager;
    private EventManager $eventManager;

    public function __construct()
    {
        self::$instance = $this;
        $this->registerChildManagers();
    }

    private function registerChildManagers(){
        $this->sessionManager = new SessionManager();
        $this->kitManager = new KitManager();
        $this->eventManager = new EventManager();
    }

    public static function getInstance(): Manager
    {
        return self::$instance;
    }

    public function getSessionManager() : SessionManager{
        return $this->sessionManager;
    }

    public function getKitManager() : KitManager{
        return $this->kitManager;
    }

    public function getEventManager() : EventManager{
        return $this->eventManager;
    }

}