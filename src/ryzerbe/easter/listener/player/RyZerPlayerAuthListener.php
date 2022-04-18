<?php

declare(strict_types=1);

namespace ryzerbe\easter\listener\player;

use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\event\player\RyZerPlayerAuthEvent;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\SkinUtils;
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
		$entity->setSkin(new Skin(uniqid(), SkinUtils::fromImage("/root/RyzerCloud/data/NPC/EasterBunnyModel.png"), "", "geometry.unknown", file_get_contents("/root/RyzerCloud/data/NPC/Bunny.geo.json")));
		$entity->setNameTagAlwaysVisible();
		$entity->setScale(1.7);
		$entity->spawnToAll();
		$entity->lookTo($player);
		$this->spawned = true;
	}
}
