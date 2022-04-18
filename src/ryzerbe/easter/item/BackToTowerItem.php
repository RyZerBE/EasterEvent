<?php
declare(strict_types=1);
namespace ryzerbe\easter\item;


use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\customitem\CustomItem;


class BackToTowerItem extends CustomItem {

	public function __construct(){
		parent::__construct(Item::get(BlockIds::DIAMOND_BLOCK)->setCustomName(TextFormat::GREEN.TextFormat::BOLD."TOWER FINISHED§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), 4);
	}

	public function onInteract(PMMPPlayer $player, Item $item): void{
		if($player->hasItemCooldown($item)) return;

		$tower = new Vector3(397.5, 83, 102.5);
		$player->teleport($tower);
		$player->getLevel()->addSound(new EndermanTeleportSound($tower), [$player]);
		$player->resetItemCooldown($item, 10);
	}
}
