<?php

namespace Zedstar16\MassiveEvent\timer;

use pocketmine\scheduler\ClosureTask;
use Zedstar16\MassiveEvent\Loader;

class Timer
{

    private static self $instance;

    private int $heartbeat;


    public function __construct(){
        self::$instance = $this;
        Loader::$instance->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void{
            Timer::getInstance()->update();
        }), 20);
    }

    public function getHeartbeat() : int{
        return $this->heartbeat;
    }

    public function update(){
        $this->heartbeat++;
    }

    public static function getInstance() : self{
        return self::$instance;
    }



}