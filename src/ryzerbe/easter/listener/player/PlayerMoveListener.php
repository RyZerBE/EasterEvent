<?php

declare(strict_types=1);

namespace ryzerbe\easter\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use ryzerbe\easter\minigame\CheckpointManager;
use ryzerbe\easter\session\PlayerSessionManager;

class PlayerMoveListener implements Listener {
    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $session = PlayerSessionManager::get($player);
        if($session === null) return;
        foreach(CheckpointManager::getInstance()->getCheckpoints() as $checkpoint) {
            if(!$player->floor()->equals($checkpoint)) continue;
            if(!$session->hasCheckpoint($checkpoint)) {
                $session->addCheckpoint($checkpoint);
                $player->playSound("random.levelup");
            }
        }
    }
}