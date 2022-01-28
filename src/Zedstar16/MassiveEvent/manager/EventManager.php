<?php

namespace Zedstar16\MassiveEvent\manager;

use Zedstar16\MassiveEvent\team\Team;

class EventManager extends Manager
{

    /*
     * Class solely for managing event related data/functions
     */


    public static $event_stage = -1;

    public const EVENT_INIT = 0;
    public const EVENT_IN_PROGRESS = 1;
    public const EVENT_OVER = 2;

    public const TIMINGS = [
        0 => self::EVENT_INIT,
        5*60 => self::EVENT_IN_PROGRESS,
        50*60 => self::EVENT_OVER
    ];

    public function handleEventStage(int $newStage){
        switch (self::EVENT_INIT){

        }
    }




}