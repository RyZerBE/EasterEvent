<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\session\PlayerSessionManager;


class PlayerQuitListener implements Listener{
	public function onQuit(PlayerQuitEvent $event){
		/** @var PMMPPlayer $player */
		$player = $event->getPlayer();

		PlayerSessionManager::deletePlayerSession($player);
		$event->setQuitMessage("");
	}
}
