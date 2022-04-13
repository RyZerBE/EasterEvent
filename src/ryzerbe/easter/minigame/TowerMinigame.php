<?php

declare(strict_types=1);

namespace ryzerbe\easter\minigame;

use pocketmine\event\Listener;

abstract class TowerMinigame implements Listener {
	abstract public function getName(): string;

	public function onUpdate(): void{}
}
