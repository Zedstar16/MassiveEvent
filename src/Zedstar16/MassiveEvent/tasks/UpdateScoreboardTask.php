<?php

namespace Zedstar16\MassiveEvent\tasks;

use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use Zedstar16\MassiveEvent\session\Session;

class UpdateScoreboardTask extends Task
{

    /** @var Session[] */
    public array $sessions = [];
    private int $tick = 1;

    private int $batch_size;

    public function __construct(array $sessions)
    {
        $this->sessions = $sessions;
        $this->batch_size = ceil(count($sessions)/19);
    }

    public function onRun(): void
    {
        if (empty($this->sessions) || !isset($this->players[0]) || $this->tick === 20) {
            if(!empty($this->sessions)){
                // Update any remaining player scoreboards that have not been processed yet;
                foreach ($this->sessions as $session){
                    $session->updateScoreboard();
                }
            }
            $this->getHandler()->cancel();
            $this->players = [];
            return;
        }

        for($i = 0; $i < $this->batch_size; $i++){
            if(isset($this->sessions[$i])) {
                $this->sessions[$i]->updateScoreboard();
                unset($this->sessions[$i]);
            }else{
                $this->getHandler()->cancel();
            }
        }

        // Reshuffle array to fill up empty indexes at start
        $sessions = $this->sessions;
        $this->sessions = [];
        foreach ($sessions as $session){
            $this->sessions[] = $session;
        }
        $this->tick++;
    }
}