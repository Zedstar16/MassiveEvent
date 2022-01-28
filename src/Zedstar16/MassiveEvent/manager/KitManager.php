<?php

namespace Zedstar16\MassiveEvent\manager;

use Zedstar16\MassiveEvent\kit\Kit;
use Zedstar16\MassiveEvent\Loader;
use Zedstar16\MassiveEvent\util\Utils;

class KitManager
{

    /*
     *  Class for managing kit related functions
     */

    /** @var Kit[] */
    private array $kits = [];

    public function __construct(){
        $config_path = Loader::$instance->getDataFolder()."kits.yml";
        if(!file_exists($config_path)){
            yaml_emit_file($config_path, []);
        }
        $register = $this->registerKits(yaml_parse_file($config_path));
        Loader::$instance->getLogger()->notice("Registered $register kits successfully");
    }

    public function registerKits(array $kitsConfig) : int{
        $kits_registered = 0;
        foreach ($kitsConfig as $kitConfig) {
            $kitData = [];
            foreach ($kitConfig["armor"] as $armorIndex => $itemString) {
                $kitData[0][$armorIndex] = Utils::itemFromString($itemString);
            }
            foreach ($kitConfig["inventory"] as $itemIndex => $itemString) {
                $kitData[1][$itemIndex] = Utils::itemFromString($itemString);
            }
            $this->kits[] = new Kit($kitConfig["name"], $kitData[0], $kitData[1]);
            $kits_registered++;
        }
        return $kits_registered;
    }

    /**
     * @return Kit[]
     */
    public function getKits() : array{
        return $this->kits;
    }

    /**
     * @param string $kitName
     * @return Kit|null
     */
    public function getKit(string $kitName) : ?Kit{
        foreach ($this->kits as $kit){
            if($kit->getName() === $kitName){
                return $kit;
            }
        }
        return null;
    }



}