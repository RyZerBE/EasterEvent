<?php
declare(strict_types=1);
namespace ryzerbe\easter;


use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\loader\ListenerDirectoryLoader;
use ryzerbe\easter\command\EggModeCommand;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\scheduler\UpdateScheduler;


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
			new EggModeCommand()
		]);
		$this->getScheduler()->scheduleRepeatingTask(new UpdateScheduler(), 1);
	}

	public static function getInstance(): Loader{
		return self::$instance;
	}

	public function getConfig(): Config{
		return self::$jsonConfig;
	}
}
