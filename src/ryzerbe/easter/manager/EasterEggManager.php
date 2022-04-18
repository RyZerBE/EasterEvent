<?php

declare(strict_types=1);

namespace ryzerbe\easter\manager;

use pocketmine\math\Vector3;
use pocketmine\utils\SingletonTrait;
use ryzerbe\core\util\Vector3Utils;
use ryzerbe\easter\Loader;

class EasterEggManager {
	use SingletonTrait;

	/** @var Vector3[]  */
	private array $easterEggLocations;

	public int $coinsPerHead = 100;

	public function __construct(){
		$config = Loader::getInstance()->getConfig();
		$this->coinsPerHead = $config->get("coins_per_egg", 100);
		$this->easterEggLocations = (array) json_decode($config->get("egg_locations", "[]"));

        foreach($this->easterEggLocations as $sVector3 => $easterEggLocation) {
            if(is_string($easterEggLocation)) {
                $this->easterEggLocations[$sVector3] = Vector3Utils::fromString($easterEggLocation);
                continue;
            }
            $this->easterEggLocations[$sVector3] = new Vector3($easterEggLocation->x, $easterEggLocation->y, $easterEggLocation->z);
        }
	}

	public function addEasterEggLocation(Vector3 $vector3){
		$this->easterEggLocations[$vector3->__toString()] = $vector3;
	}

	public function removeEasterEggLocation(Vector3 $vector3){
		unset($this->easterEggLocations[$vector3->__toString()]);
	}

	public function isEasterEgg(Vector3 $vector3): bool{
		return isset($this->easterEggLocations[$vector3->__toString()]);
	}

	/**
	 * @return Vector3[]
	 */
	public function getEasterEggLocations(): array{
		return $this->easterEggLocations;
	}

	public function saveEggLocations(){
		Loader::getInstance()->getConfig()->set("egg_locations", json_encode($this->easterEggLocations));
		Loader::getInstance()->getConfig()->save();
	}
}
