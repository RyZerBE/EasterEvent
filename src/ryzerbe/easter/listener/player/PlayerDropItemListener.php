<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;


class PlayerDropItemListener implements Listener{

	public function onDrop(PlayerDropItemEvent $event){
		$event->setCancelled();
	}
}
