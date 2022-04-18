<?php
declare(strict_types=1);
namespace ryzerbe\easter\listener\player;


use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;


class PlayerCraftItemListener implements Listener{

	public function onCraft(CraftItemEvent $event){
		$event->setCancelled();
	}
}
