<?php
declare(strict_types=1);
namespace ryzerbe\easter\scheduler;

use javamapconverter\entity\SkullEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use ryzerbe\easter\session\PlayerSessionManager;


class UpdateScheduler extends Task{
	public function onRun(int $currentTick){
		if($currentTick % 60 === 0) {
			$level = Server::getInstance()->getDefaultLevel();
			foreach ($level->getEntities() as $entity) {
				if(!$entity instanceof SkullEntity) continue;
				$position = $entity->asPosition();

				//TODO: ADD COOL EFFECT / PARTICLE TO THE EGG
			}
		}

		if($currentTick % 20 !== 0) return;

		foreach (PlayerSessionManager::getSessions() as $playerSession) {
			$playerSession->onUpdate($currentTick);
		}
	}
}
