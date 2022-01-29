<?php

namespace Zedstar16\MassiveEvent\form;

use pocketmine\player\Player;
use Zedstar16\MassiveEvent\lib\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\MassiveEvent\manager\Manager;

class KitSelectionForm extends BaseForm
{


    public function sendForm(){
        $player = $this->player;
        $kitMgr = Manager::getInstance()->getKitManager();
        $kits = [];
        foreach ($kitMgr->getKits() as $kit){
            $kits[] = $kit->getName();
        }
        $form = new SimpleForm(function (Player $p, $data){
            if($data !== null){
                new KitConfirmForm($p, $data);
            }
        });
        $form->setTitle("§1Kit Selection");
        $form->setContent("Select a kit");
        foreach ($kits as $kit){
            $form->addButton($kit, -1, "", $kit);
        }
        $player->sendForm($form);
    }



}