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
        $result = [];
        $username = $player->getName();
        if(!isset($this->last_messages[$username])){
            return [true];
        }
        if(similar_text($this->last_messages[$username], $message) > 80){
            return [false, "§cYour message is too similar to your previous message"];
        }

        if(!$this->cooldowns[$username]){
            $this->cooldowns[$username] = microtime(true);
            return [true];
        }
        $cd = microtime(true) - $this->cooldowns[$username];
        if($cd > 1){
            $this->cooldowns[$username] = microtime(true);
            return [true];
        }else{
            return [false, "§cPlease wait another §f".round($cd, 1)."s §cbefore attempting this action again"];
        }
    }

}