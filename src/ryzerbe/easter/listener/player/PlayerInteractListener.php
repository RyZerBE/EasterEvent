<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\player;

use javamapconverter\skull\SkullChunkManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\Loader;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\session\PlayerSessionManager;


class PlayerInteractListener implements Listener{

	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof PMMPPlayer) return;

		$block = $event->getBlock();
		$blockVec = $block->asVector3();

		$manager = EasterEggManager::getInstance();
		$playerSession = PlayerSessionManager::get($player);
		if($playerSession === null) {
			$event->setCancelled();
			return;
		}

		$skullChunk = SkullChunkManager::getInstance()->getChunkByXZ($block->getLevel(), $block->x, $block->z);
		if($skullChunk === null) return;

		if($skullChunk->isSkull($block)) {
			if($manager->isEasterEgg($blockVec)) {
				if($player->hasDelay("easter_egg")) return;

				$player->addDelay("easter_egg", 1);
				if($playerSession->alreadyFound($blockVec)) {
					$player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("egg-already-found", $player));
					return;
				}
				$playerSession->foundEasterEgg($blockVec);
			}
		}
	}
}
