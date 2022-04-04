<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\block;

use javamapconverter\skull\SkullChunkManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use ryzerbe\easter\Loader;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\session\PlayerSessionManager;


class BlockBreakListener implements Listener{

	public function onBreak(BlockBreakEvent $event){
		$block = $event->getBlock();
		$blockVec = $block->asVector3();
		$player = $event->getPlayer();
		$manager = EasterEggManager::getInstance();
		$playerSession = PlayerSessionManager::get($player);
		if($playerSession === null) {
			$event->setCancelled();
			return;
		}

		if(!$playerSession->isEggMode()) {
			$event->setCancelled();
			return;
		}

		$skullChunk = SkullChunkManager::getInstance()->getChunkByXZ($block->getLevel(), $block->x, $block->z);
		if($skullChunk === null) return;

		if($skullChunk->isSkull($block)) {
			if($manager->isEasterEgg($blockVec)) {
				$manager->removeEasterEggLocation($blockVec);
				$player->sendMessage(Loader::PREFIX."Das Egg wurde ".TextFormat::RED.TextFormat::BOLD."entfernt");
			}
			return;
		}
	}
}
