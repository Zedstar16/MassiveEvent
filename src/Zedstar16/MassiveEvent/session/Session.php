<?php

namespace Zedstar16\MassiveEvent\session;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use Zedstar16\MassiveEvent\kit\Kit;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;
use Zedstar16\MassiveEvent\scoreboard\Scoreboard;
use Zedstar16\MassiveEvent\scoreboard\ScoreFactory;
use Zedstar16\MassiveEvent\tasks\DeathTitleTask;
use Zedstar16\MassiveEvent\team\Team;
use Zedstar16\MassiveEvent\team\TeamHandler;
use Zedstar16\MassiveEvent\util\Utils;

class Session
{
    private Player $player;

    private ?string $selected_kit = null;

    private string $input_method;

    private Scoreboard $scoreboard;

    public int $team_id;

    public function __construct(Player $player, int $team_id)
    {
        $this->player = $player;
        $this->team_id = $team_id;
        $this->input_method = Utils::translateInputMethod($this->player->getNetworkSession()->getPlayerInfo()->getExtraData()["CurrentInputMode"] ?? 69);
        $this->scoreboard = new Scoreboard($player);
    }

    public function getPlayer() {
        return $this->player;
    }


    public function updateScoreboard()
    {
        ScoreFactory::setScore($this->player, "§l§6Ownage §eEvent");
        foreach ($this->scoreboard->getScoreLines(EventManager::$event_stage) as $line_number => $line){
            ScoreFactory::setScoreLine($this->player, $line_number+1, $line);
        }
    }

    public function updateScoreTag(){
        $color = $this->getTeam()->getTeamColor();
        $this->player->setNameTag($color.$this->player->getName());
        $this->player->setScoreTag(Utils::formatHealthString($this->player->getHealth())."§8| §7".$this->input_method);
    }

    public function handleDeath()
    {
        if(EventManager::$event_stage !== EventManager::EVENT_INIT) {
            $this->player->setGamemode(GameMode::SPECTATOR());

            // Will teleport player to a slightly offset position to where they died to give a more spectator like feel
            $this->player->teleport($this->player->getPosition()->add(1, 2, 0));
            Loader::$instance->getScheduler()->scheduleRepeatingTask(new DeathTitleTask($this->player), 20);
        }
    }

    public function getTeam(): Team
    {
        return TeamHandler::getInstance()->getTeam($this->team_id);
    }

    public function getInputMethod() : string{
        return $this->input_method;
    }

    public function getKills(): int
    {
        return $this->getTeam()->kills[$this->player->getName()] ?? 0;
    }

    public function getDeaths()
    {
        return $this->getTeam()->deaths[$this->player->getName()] ?? 0;
    }

    public function setSelectedKit(string $kit)
    {
        $this->selected_kit = $kit;
    }

    public function getSelectedKit(): ?Kit
    {
        $selected_kit = $this->selected_kit ?? "NoDebuff";
        return Manager::getInstance()->getKitManager()->getKit($selected_kit) ?? Manager::getInstance()->getKitManager()->getKit("NoDebuff");
    }


}