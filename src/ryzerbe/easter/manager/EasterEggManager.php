<?php
declare(strict_types=1);
namespace ryzerbe\easter\manager;


use pocketmine\math\Vector3;
use pocketmine\utils\SingletonTrait;
use ryzerbe\core\provider\ChatEmojiProvider;
use ryzerbe\easter\Loader;


class EasterEggManager {
	use SingletonTrait;

	/** @var Vector3[]  */
	private array $easterEggLocations = [];

	public int $coinsPerHead = 100;

	public function __construct(){
		$config = Loader::getInstance()->getConfig();
		$this->coinsPerHead = $config->get("coins_per_egg", 100);
		$this->easterEggLocations = (array) json_decode($config->get("egg_locations", "[]"));
	}

	/**
	 * Function addEasterEggLocation
	 * @param Vector3 $vector3
	 * @return void
	 */
	public function addEasterEggLocation(Vector3 $vector3){
		$this->easterEggLocations[$vector3->__toString()] = $vector3;
	}

	/**
	 * Function removeEasterEggLocation
	 * @param Vector3 $vector3
	 * @return void
	 */
	public function removeEasterEggLocation(Vector3 $vector3){
		unset($this->easterEggLocations[$vector3->__toString()]);
	}

	/**
	 * Function isEasterEgg
	 * @param Vector3 $vector3
	 * @return bool
	 */
	public function isEasterEgg(Vector3 $vector3): bool{
		return isset($this->easterEggLocations[$vector3->__toString()]);
	}

	/**
	 * Function getEasterEggLocations
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
