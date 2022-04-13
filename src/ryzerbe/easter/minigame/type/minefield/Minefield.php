<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame\type\minefield;

use pocketmine\block\WeightedPressurePlateHeavy;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;
use ryzerbe\easter\minigame\TowerMinigame;
use ryzerbe\easter\minigame\trait\AxisAlignedBBTrait;

class Minefield extends TowerMinigame {
    use AxisAlignedBBTrait;

    public function __construct(
        protected Vector3 $firstVector3,
        protected Vector3 $secondVector3,
        protected Location $spawnLocation
    ){
        $this->spawnLocation->level = Server::getInstance()->getDefaultLevel();
        $this->constructAxisAlignedBB($this->firstVector3, $this->secondVector3);
    }

    public function getName(): string{
        return "Minefield";
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if(!$player->isOnGround() || !$this->getAxisAlignedBB()->isVectorInside($player)) return;
        $level = $player->getLevel();
        $block = $level->getBlock($player->add(0, 0.75));
        if($block instanceof WeightedPressurePlateHeavy) {
            $level->broadcastLevelEvent($player, LevelEventPacket::EVENT_PARTICLE_EXPLOSION, 2);
            $level->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_EXPLODE);
            $player->teleport($this->spawnLocation);
        }
    }
}