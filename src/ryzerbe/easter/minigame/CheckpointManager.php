<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame;

use pocketmine\utils\SingletonTrait;

class CheckpointManager {
    use SingletonTrait;

    /** @var TowerCheckpoint[]  */
    protected array $checkpoints = [];

    public function getCheckpoints(): array{
        return $this->checkpoints;
    }

    public function registerCheckpoint(TowerCheckpoint $checkpoint): void {
        $this->checkpoints[] = $checkpoint;
    }
}