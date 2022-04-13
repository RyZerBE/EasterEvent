<?php

declare(strict_types=1);

namespace ryzerbe\easter;

use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\loader\ListenerDirectoryLoader;
use ryzerbe\easter\command\BuildModeCommand;
use ryzerbe\easter\command\EggModeCommand;
use ryzerbe\easter\command\SaveWorldCommand;
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
            new BuildModeCommand(),
		]);
		$this->getScheduler()->scheduleRepeatingTask(new UpdateScheduler(), 1);

        $level = Server::getInstance()->getDefaultLevel();
        $level->setAutoSave(false);
        $level->setTime(1000);
        $level->stopTime();

        new MinigameRegistry();
        new CheckpointRegistry();

        new Hologram(new Vector3(421.5, 20, 88.5), "§r§7§l§o-- §r§6§l§oTower of pain §r§7§l§o--", false);
	}

	public static function getInstance(): Loader{
		return self::$instance;
	}

	public function getConfig(): Config{
		return self::$jsonConfig;
	}
}
