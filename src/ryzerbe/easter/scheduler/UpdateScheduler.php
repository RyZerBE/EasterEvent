<?php

declare(strict_types=1);

namespace ryzerbe\easter\scheduler;

use pocketmine\level\particle\DustParticle;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use ryzerbe\easter\manager\EasterEggManager;
use ryzerbe\easter\minigame\MinigameManager;
use ryzerbe\easter\session\PlayerSessionManager;

class UpdateScheduler extends Task{
	public function onRun(int $currentTick){
		if($currentTick % 15 === 0) {
			$level = Server::getInstance()->getDefaultLevel();
            foreach(EasterEggManager::getInstance()->getEasterEggLocations() as $vector3) {
                $particle = new DustParticle($vector3->add(0.5 + mt_rand(-5, 5) / 10, mt_rand(0, 10) / 10, 0.5 + mt_rand(-5, 5) / 10), mt_rand(), mt_rand(), mt_rand());
                $pk = $particle->encode();
                foreach(Server::getInstance()->getOnlinePlayers() as $player) {
                    $session = PlayerSessionManager::get($player);
                    if($session === null) continue;
                    if(!$session->alreadyFound($vector3)) {
                        $player->sendDataPacket($pk);
                    }
                }
            }
		}

        foreach(MinigameManager::getInstance()->getMinigames() as $minigame) {
            $minigame->onUpdate();
        }

		if($currentTick % 20 !== 0) return;
		foreach (PlayerSessionManager::getSessions() as $playerSession) {
			$playerSession->onUpdate($currentTick);
		}
	}
}
