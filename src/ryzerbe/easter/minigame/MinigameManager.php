<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ryzerbe\easter\Loader;

class MinigameManager {
    use SingletonTrait;

    /** @var TowerMinigame[]  */
    private array $minigames = [];

    public function getMinigames(): array{
        return $this->minigames;
    }

    public function registerMinigame(TowerMinigame $minigame): void {
        Server::getInstance()->getPluginManager()->registerEvents($minigame, Loader::getInstance());
        $this->minigames[] = $minigame;
    }
}