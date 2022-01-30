<?php

namespace Zedstar16\MassiveEvent\chat;

use pocketmine\player\Player;

class ChatHandler
{

    private $cooldowns = [];

    private $last_messages = [];


    /**
     *  Determines whether a player is on message cooldown, and also if they're trying to spam the same message
     *  Will return an array containing a bool value for whether the message should be sent, and a message for why the message cannot be sent if applicable
     *
     * @param Player $player
     * @param string $message
     * @return array
     */
    public function processMessage(Player $player, string $message) : array{
        $username = $player->getName();

        if(!isset($this->last_messages[$username])){
            $this->last_messages[$username] = $message;
            return [true];
        }
        similar_text($this->last_messages[$username], $message, $percent);
        if($percent > 75){
            return [false, "§cYour message is too similar to your previous message"];
        }
        $this->last_messages[$username] = $message;

        if(!isset($this->cooldowns[$username])){
            $this->cooldowns[$username] = microtime(true);
            return [true];
        }

        $cd = microtime(true) - $this->cooldowns[$username];
        if($cd < 1.5){
            return [false, "§cPlease wait another §f".round($cd, 1)."s §cbefore attempting this action again"];
        }else{
            $this->cooldowns[$username] = microtime(true);
            return [true];
        }
    }

}