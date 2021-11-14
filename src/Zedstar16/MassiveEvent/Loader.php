<?php

declare(strict_types=1);

namespace Zedstar16\MassiveEvent;

use pocketmine\plugin\PluginBase;
use Zedstar16\MassiveEvent\listener\EventListener;

class Loader extends PluginBase{

    /** @var Loader|null */
    public static ?Loader $instance = null;

    protected function onEnable(): void
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

}
