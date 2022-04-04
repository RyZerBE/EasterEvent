<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\player;


use pocketmine\event\Listener;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\session\PlayerSessionManager;


class RyZerPlayerAuthListener implements Listener{

	public function onAuth(RyZerPlayerAuthEvent $event){
		/** @var PMMPPlayer $player */
		$player = $event->getPlayer();

		PlayerSessionManager::createPlayerSession($player);
	}
}
