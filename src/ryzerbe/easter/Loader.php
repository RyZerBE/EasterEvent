<?php

declare(strict_types=1);

namespace ryzerbe\easter;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ryzerbe\core\RyZerBE;
use ryzerbe\core\util\customitem\CustomItemManager;
use ryzerbe\core\util\loader\ListenerDirectoryLoader;
use ryzerbe\easter\command\BuildModeCommand;
use ryzerbe\easter\command\EggModeCommand;
use ryzerbe\easter\command\SaveWorldCommand;
use ryzerbe\easter\entity\EasterBunnyEntity;
use ryzerbe\easter\item\BackToCheckPointItem;
use ryzerbe\easter\item\BackToSpawnItem;
use ryzerbe\easter\item\BackToTowerItem;
use ryzerbe\easter\item\HidePlayerItem;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\registry\CheckpointRegistry;
use ryzerbe\easter\registry\MinigameRegistry;
use ryzerbe\easter\scheduler\UpdateScheduler;
use ryzerbe\easter\util\Hologram;

class Loader extends PluginBase {

	public static ?Config $jsonConfig = null;

	public const PREFIX = TextFormat::RED.TextFormat::BOLD."Easter ".TextFormat::RESET.TextFormat::GRAY;

	private static Loader $instance;

	public function onEnable(){
		self::$instance = $this;
		try {
			ListenerDirectoryLoader::load($this, $this->getFile(), __DIR__ . "/listener/");
		} catch (\ReflectionException $e) {}

		$this->saveResource("config.json");
		self::$jsonConfig = new Config($this->getDataFolder()."config.json");
		EasterEggManager::getInstance();


		Server::getInstance()->getCommandMap()->registerAll("easter", [
			new EggModeCommand(),
            new SaveWorldCommand(),
            new BuildModeCommand()
		]);
		$this->getScheduler()->scheduleRepeatingTask(new UpdateScheduler(), 1);

        $level = Server::getInstance()->getDefaultLevel();
        $level->setAutoSave(false);
        $level->setTime(1000);
        $level->stopTime();

        new MinigameRegistry();
        new CheckpointRegistry();

        new Hologram(new Vector3(421.5, 21, 88.5), "§r§7§l§o-- §r§6§l§oTower of pain §r§7§l§o--", false);
        new Hologram(new Vector3(397.5, 83, 102.5), "§r§7§l§o-- §r§d§l§oFINISH THE TOWER §r§7§l§o--", false);
        new Hologram(new Vector3(421.5, 21, 92.5), "§r§7§l§o -- §r§e§l§o Tower Rewards §r§7§l§o --\n\n§7=> §e50.000 Coins\n§7=> §eEaster Egg as Bedwars Stick\n§7=> §eAccess to a special giveaway on Discord\n§7=> §eThe fastest players get access to /setstatus in the lobby", false);

		CustomItemManager::getInstance()->registerCustomItem(new BackToCheckPointItem());
		CustomItemManager::getInstance()->registerCustomItem(new BackToTowerItem());
		CustomItemManager::getInstance()->registerCustomItem(new BackToSpawnItem());
		CustomItemManager::getInstance()->registerCustomItem(new HidePlayerItem());
		Entity::registerEntity(EasterBunnyEntity::class, true);
	}

	public static function getInstance(): Loader{
		return self::$instance;
	}

	public function getConfig(): Config{
		return self::$jsonConfig;
	}
}
