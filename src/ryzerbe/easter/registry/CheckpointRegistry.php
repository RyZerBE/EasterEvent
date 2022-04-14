<?php

declare(strict_types=1);

namespace ryzerbe\easter\registry;

use ryzerbe\easter\minigame\CheckpointManager;
use ryzerbe\easter\minigame\TowerCheckpoint as Checkpoint;

class CheckpointRegistry {
    public function __construct(){
        self::register(new Checkpoint(421, 19, 88));
        self::register(new Checkpoint(390, 30, 102));
        self::register(new Checkpoint(400, 33, 89));
        self::register(new Checkpoint(416, 35, 88));
        self::register(new Checkpoint(410, 32, 112));
        self::register(new Checkpoint(400, 32, 103));
        self::register(new Checkpoint(419, 39, 109));
        self::register(new Checkpoint(413, 42, 112));
        self::register(new Checkpoint(406, 43, 117));
        self::register(new Checkpoint(398, 45, 114));
        self::register(new Checkpoint(392, 45, 113));
        self::register(new Checkpoint(391, 44, 101));
        self::register(new Checkpoint(395, 45, 95));
        self::register(new Checkpoint(401, 44, 91));
        self::register(new Checkpoint(407, 46, 86));
        self::register(new Checkpoint(417, 44, 86));
        self::register(new Checkpoint(422, 45, 93));
        self::register(new Checkpoint(426, 59, 102));
        self::register(new Checkpoint(427, 67, 102));
    }

    public static function register(Checkpoint $checkpoint): void {
        CheckpointManager::getInstance()->registerCheckpoint($checkpoint);
    }
}