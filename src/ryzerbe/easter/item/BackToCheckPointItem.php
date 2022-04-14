<?php
declare(strict_types=1);
namespace ryzerbe\easter\item;


use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\customitem\CustomItem;
use ryzerbe\easter\Loader;
use ryzerbe\easter\session\PlayerSessionManager;


class BackToCheckPointItem extends CustomItem{

	public function __construct(){
		parent::__construct(Item::get(BlockIds::GOLD_BLOCK)->setCustomName(TextFormat::GREEN.TextFormat::BOLD."CHECKPOINT§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), 4);
	}

	public function onInteract(PMMPPlayer $player, Item $item): void{
		if($player->hasItemCooldown($item)) return;
		$playerSession = PlayerSessionManager::get($player);
		if($playerSession === null) return;

		$checkpoint = $playerSession->getLastCheckPoint();
		if($checkpoint === null) {
			$player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("easter-no-checkpoint", $player));
			return;
		}

		$player->teleport($checkpoint);
		$player->getLevel()->addSound(new EndermanTeleportSound($checkpoint), [$player]);
		$player->resetItemCooldown($item, 10);
	}
}
