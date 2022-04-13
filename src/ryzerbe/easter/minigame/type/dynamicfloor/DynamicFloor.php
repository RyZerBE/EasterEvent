<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame\type\dynamicfloor;

use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\Server;
use ryzerbe\easter\minigame\TowerMinigame;
use ryzerbe\easter\minigame\trait\AxisAlignedBBTrait;

class DynamicFloor extends TowerMinigame {
    use AxisAlignedBBTrait;

    protected int $nextUpdateTick = 0;

    public function __construct(
        protected Vector3 $firstVector3,
        protected Vector3 $secondVector3
    ){
        $this->constructAxisAlignedBB($this->firstVector3, $this->secondVector3);
    }

    public function getName(): string{
        return "Dynamic Floor";
    }

    public function onUpdate(): void{
        $tick = Server::getInstance()->getTick();
        if($tick < $this->nextUpdateTick) return;
        $this->nextUpdateTick = $tick + mt_rand(15, 23);
        $axisAlignedBB = $this->axisAlignedBB;
        $level = Server::getInstance()->getDefaultLevel();

        for($x = $axisAlignedBB->minX; $x <= $axisAlignedBB->maxX; $x++) {
            for($z = $axisAlignedBB->minZ; $z <= $axisAlignedBB->maxZ; $z++) {
                if(mt_rand(0, 100) >= 50) {
                    $level->setBlockIdAt((int)$x, (int)$axisAlignedBB->minY, (int)$z, 0);
                    continue;
                }
                $level->setBlockIdAt((int)$x, (int)$axisAlignedBB->minY, (int)$z, BlockIds::TERRACOTTA);
                $level->setBlockDataAt((int)$x, (int)$axisAlignedBB->minY, (int)$z, mt_rand(0, 15));
            }
        }
    }
}