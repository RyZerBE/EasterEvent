<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame;

use pocketmine\math\Vector3;
use ryzerbe\easter\util\Hologram;

class TowerCheckpoint extends Vector3 {
    public function __construct($x = 0, $y = 0, $z = 0){
        parent::__construct($x, $y, $z);
        new Hologram($this->floor()->add(0.5, 1, 0.5), "§r§a§l§oCheckpoint");
    }
}