<?php

namespace Zedstar16\MassiveEvent\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Server;
use pocketmine\player\Player;

class ScoreFactory{

    /** @var string */
    private const objectiveName = "objective";
    /** @var string */
    private const criteriaName = "dummy";
    /** @var int */
    private const MIN_LINES = 1;
    /** @var int */
    private const MAX_LINES = 15;
    /** @var int */
    public const SORT_ASCENDING = 0;
    /** @var string */
    public const SLOT_SIDEBAR = "sidebar";
    /** @var array */
    private static array $scoreboards = [];

    // ScoreFactory class from part of ScoreFactory virion by JackMD/Ifera

    /**
     * Adds a Scoreboard to the player if he doesn't have one.
     * Can also be used to update a scoreboard.
     *
     * @param Player $player
     * @param string $displayName
     * @param int    $slotOrder
     * @param string $displaySlot
     */
    public static function setScore(Player $player, string $displayName, int $slotOrder = self::SORT_ASCENDING, string $displaySlot = self::SLOT_SIDEBAR): void{
        try{
        if($player->isConnected()) {
            if (isset(self::$scoreboards[$player->getName()])) {
                self::removeScore($player);
            }

            $pk = new SetDisplayObjectivePacket();
            $pk->displaySlot = $displaySlot;
            $pk->objectiveName = self::objectiveName;
            $pk->displayName = $displayName;
            $pk->criteriaName = self::criteriaName;
            $pk->sortOrder = $slotOrder;
            $player->getNetworkSession()->sendDataPacket($pk);

            self::$scoreboards[$player->getName()] = self::objectiveName;
        }
        }catch (\Throwable $err){}
    }

    /**
     * Removes a scoreboard from the player specified.
     *
     * @param Player $player
     */
    public static function removeScore(Player $player): void{
        try {
            if ($player->isConnected()) {
                $objectiveName = self::objectiveName;
                $pk = new RemoveObjectivePacket();
                $pk->objectiveName = $objectiveName;
                $player->getNetworkSession()->sendDataPacket($pk);

                if (isset(self::$scoreboards[($player->getName())])) {
                    unset(self::$scoreboards[$player->getName()]);
                }
            }
        }catch (\Throwable $err){}
    }

    /**
     * Returns an array consisting of a list of the players using scoreboard.
     *
     * @return array
     */
    public static function getScoreboards(): array{
        return self::$scoreboards;
    }

    /**
     * Returns true or false if a player has a scoreboard or not.
     *
     * @param Player $player
     * @return bool
     */
    public static function hasScore(Player $player): bool{
        return isset(self::$scoreboards[$player->getName()]);
    }

    /**
     * Set a message at the line specified to the players scoreboard.
     *
     * @param Player $player
     * @param int    $line
     * @param string $message
     */
    public static function setScoreLine(Player $player, int $line, string $message): void{
        try{
        if($player->isConnected()) {
            if (!isset(self::$scoreboards[$player->getName()])) {
                Server::getInstance()->getLogger()->error("Cannot set a score to a player with no scoreboard");
                return;
            }
            if ($line < self::MIN_LINES || $line > self::MAX_LINES) {
                Server::getInstance()->getLogger()->error("Score must be between the value of " . self::MIN_LINES . " to " . self::MAX_LINES . ".");
                Server::getInstance()->getLogger()->error($line . " is out of range");
                return;
            }

            $entry = new ScorePacketEntry();
            $entry->objectiveName = self::objectiveName;
            $entry->type = $entry::TYPE_FAKE_PLAYER;
            $entry->customName = $message;
            $entry->score = $line;
            $entry->scoreboardId = $line;

            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_CHANGE;
            $pk->entries[] = $entry;
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        }catch (\Throwable $err){}
    }
}