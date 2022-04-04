<?php
declare(strict_types=1);
namespace ryzerbe\easter\scheduler;

use pocketmine\scheduler\Task;
use ryzerbe\easter\session\PlayerSessionManager;


class UpdateScheduler extends Task{
	public function onRun(int $currentTick){
		if($currentTick % 20 !== 0) return;

		foreach (PlayerSessionManager::getSessions() as $playerSession) {
			$playerSession->onUpdate($currentTick);
		}

		//TODO: Spawn cool particle about the egg
	}
}
