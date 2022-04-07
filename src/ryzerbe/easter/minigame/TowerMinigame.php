<?php
declare(strict_types=1);
namespace ryzerbe\easter\minigame;


abstract class TowerMinigame{

	abstract public function getName(): string;

	public function onUpdate(): void{}
}
