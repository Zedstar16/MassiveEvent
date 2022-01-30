<?php

namespace Zedstar16\MassiveEvent\form;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\lib\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\MassiveEvent\manager\EventManager;
use Zedstar16\MassiveEvent\manager\Manager;

class KitConfirmForm extends BaseForm
{

    private string $kit;

    public function __construct(Player $player, string $kit)
    {
        $this->kit = $kit;
        parent::__construct($player);
    }

    public function sendForm()
    {
        $player = $this->player;
        $kit_name = $this->kit;
        $kitMgr = Manager::getInstance()->getKitManager();
        $form = new SimpleForm(function (Player $p, $data) use ($kit_name) {
            if ($data === 0) {
                Manager::getInstance()->getSessionManager()->getSession($p)->setSelectedKit($kit_name);
                if (EventManager::$event_stage === EventManager::EVENT_INIT) {
                    $p->sendMessage("§aKit Selected");
                } else {
                    $p->sendMessage("§aNew Kit Selected, your kit will change after you die");
                }
            } else {
                // Send them back to menu regardless of whether they want to close it or not
                new KitSelectionForm($p);
            }
        });

        $kit = $kitMgr->getKit($kit_name);
        $form->setContent($kit->getStringContentsList());
        $form->addButton("§2Select Kit");
        $form->addButton("§4Back");
        $player->sendForm($form);
    }


}