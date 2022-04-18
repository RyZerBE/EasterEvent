<?php
declare(strict_types=1);
namespace ryzerbe\easter\item;


use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\customitem\CustomItem;


class BackToSpawnItem extends CustomItem{


	public function __construct(){
		parent::__construct(Item::get(ItemIds::ARROW)->setCustomName(TextFormat::RED.TextFormat::BOLD."Back to spawn§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), 8);
	}

	public function onInteract(PMMPPlayer $player, Item $item): void{
		if($player->hasItemCooldown($item)) return;
		$player->resetItemCooldown($item, 10);

		$spawn = $player->getServer()->getDefaultLevel()->getSafeSpawn();
		$player->teleport($spawn);
		$player->getLevel()->addSound(new EndermanTeleportSound($spawn), [$player]);
	}
}
