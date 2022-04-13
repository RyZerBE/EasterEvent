<?php

declare(strict_types=1);

namespace ryzerbe\easter\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\session\PlayerSessionManager;

class EggModeCommand extends Command {
	public function __construct(){
		parent::__construct("eggmode", "Toggle Egg Mode", "", []);
		$this->setPermission("ryzer.admin");
		$this->setPermissionMessage(TextFormat::RED."The command is only for easter bunnies! :3");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$sender instanceof PMMPPlayer) return;
		if(!$this->testPermission($sender)) return;

		$session = PlayerSessionManager::get($sender);
		if($session === null) return;

		if(!$session->toggleEggMode()) {
			EasterEggManager::getInstance()->saveEggLocations();
		}
	}
}
