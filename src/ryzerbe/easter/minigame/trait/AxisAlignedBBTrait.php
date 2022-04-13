<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame\trait;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

trait AxisAlignedBBTrait {
    protected AxisAlignedBB $axisAlignedBB;
    
    protected function constructAxisAlignedBB(Vector3 $firstVector3, Vector3 $secondVector3): void {
        $this->axisAlignedBB = new AxisAlignedBB(
            (int)min($firstVector3->x, $secondVector3->x),
            (int)min($firstVector3->y, $secondVector3->y),
            (int)min($firstVector3->z, $secondVector3->z),
            (int)max($firstVector3->x, $secondVector3->x),
            (int)max($firstVector3->y, $secondVector3->y),
            (int)max($firstVector3->z, $secondVector3->z)
        );
    }

    public function getAxisAlignedBB(): AxisAlignedBB{
        return $this->axisAlignedBB;
    }
}