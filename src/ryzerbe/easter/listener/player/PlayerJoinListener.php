<?php

declare(strict_types=1);

namespace ryzerbe\easter\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;


class PlayerJoinListener implements Listener{

	public function onJoin(PlayerJoinEvent $event){
        $event->getPlayer()->setGamemode(2);
		$event->setJoinMessage("");
	}
}
