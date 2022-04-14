<?php

declare(strict_types=1);

namespace ryzerbe\easter\listener\player;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\entity\EasterBunnyEntity;
use ryzerbe\easter\session\PlayerSessionManager;

class RyZerPlayerAuthListener implements Listener{

	private bool $spawned = false;

	public function onAuth(RyZerPlayerAuthEvent $event){
		/** @var PMMPPlayer $player */
		$player = $event->getPlayer();

		PlayerSessionManager::createPlayerSession($player);
		if($this->spawned) return;

		$entity = new EasterBunnyEntity(Server::getInstance()->getDefaultLevel(), Entity::createBaseNBT(new Vector3(474.5, 19, 10.5)));
		$entity->setNameTag(TextFormat::RED.TextFormat::BOLD."EASTER BUNNY");
		$entity->setNameTagAlwaysVisible();
		$entity->spawnToAll();
		$this->spawned = true;
	}
}
