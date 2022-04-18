<?php

declare(strict_types=1);

namespace ryzerbe\easter\command;

use javamapconverter\listener\BlockPlaceListener;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
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

		if(isset($args[0])) {
			if($args[0] === "fix") {
				$i = 0;
				foreach (EasterEggManager::getInstance()->getEasterEggLocations() as $vector3) {
					$block = Server::getInstance()->getDefaultLevel()->getBlock($vector3);
					if($block->getId() === BlockIds::SKULL_BLOCK) continue;

					Server::getInstance()->getDefaultLevel()->setBlock($vector3, Block::get(BlockIds::SKULL_BLOCK, 1));
					$i++;
					$sender->sendMessage("Fix ".$vector3->__toString());
				}

				$sender->sendMessage("Fixed $i eggs.");
			}
			return;
		}

		if(isset($args[0])) $session = PlayerSessionManager::get($args[0]);
		else $session = PlayerSessionManager::get($sender);

		if($session === null) return;

		if(!$session->toggleEggMode()) {
			EasterEggManager::getInstance()->saveEggLocations();
		}
	}
}
