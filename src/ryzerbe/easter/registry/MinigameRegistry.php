<?php

declare(strict_types=1);

namespace ryzerbe\easter\registry;

use pocketmine\level\Location;
use pocketmine\math\Vector3;
use ryzerbe\easter\minigame\MinigameManager;
use ryzerbe\easter\minigame\TowerMinigame;
use ryzerbe\easter\minigame\type\dynamicfloor\DynamicFloor;
use ryzerbe\easter\minigame\type\minefield\Minefield;

class MinigameRegistry {
    public function __construct(){
        self::register(new DynamicFloor(new Vector3(390, 68, 105), new Vector3(399, 68, 99)));
        self::register(new DynamicFloor(new Vector3(401, 67, 105), new Vector3(413, 67, 99)));
        self::register(new DynamicFloor(new Vector3(415, 66, 105), new Vector3(424, 66, 99)));

        self::register(new Minefield(new Vector3(427, 62, 82), new Vector3(387, 57, 122), new Location(426.5, 59, 102.5, 90, 0)));
    }

    public static function register(TowerMinigame $minigame): void {
        MinigameManager::getInstance()->registerMinigame($minigame);
    }
}