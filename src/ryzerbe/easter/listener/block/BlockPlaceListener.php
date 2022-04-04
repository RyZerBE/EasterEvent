<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\block;

use javamapconverter\utils\ItemUtils;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use ryzerbe\easter\Loader;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\session\PlayerSessionManager;


class BlockPlaceListener implements Listener{

	public function onPlace(BlockPlaceEvent $event){
		$block = $event->getBlock();
		$item = $event->getItem();
		$player = $event->getPlayer();
		$playerSession = PlayerSessionManager::get($player);
		if($playerSession === null) {
			$event->setCancelled();
			return;
		}

		if(!$playerSession->isEggMode()) {
			$event->setCancelled();
			return;
		}

		if(ItemUtils::hasItemTag($item, "head")) {
			EasterEggManager::getInstance()->addEasterEggLocation($block->asVector3());
			$player->sendMessage(Loader::PREFIX."Das Egg wurde ".TextFormat::GREEN.TextFormat::BOLD."registriert");
			return;
		}
	}
}
