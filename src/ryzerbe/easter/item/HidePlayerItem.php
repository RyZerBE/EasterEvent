<?php
declare(strict_types=1);
namespace ryzerbe\easter\item;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\customitem\CustomItem;
use ryzerbe\easter\session\PlayerSessionManager;

class HidePlayerItem extends CustomItem {

	public function __construct(){
		parent::__construct(Item::get(ItemIds::GHAST_TEAR)->setCustomName(TextFormat::RED.TextFormat::BOLD."Hide players§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]"), 8);
	}

	public function onInteract(PMMPPlayer $player, Item $item): void{
		if($player->hasItemCooldown($item)) return;
		$player->resetItemCooldown($item, 10);
		$playerSession = PlayerSessionManager::get($player);
		if($playerSession === null) return;

		$item = $this->getItem();
		if($playerSession->hidePlayer) {
			foreach ($player->getServer()->getOnlinePlayers() as $onlinePlayer) {
				$player->showPlayer($onlinePlayer);
			}
			$playerSession->hidePlayer = false;
			$item->setCustomName(TextFormat::RED.TextFormat::BOLD."Hide players§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]");
		}else {
			foreach ($player->getServer()->getOnlinePlayers() as $onlinePlayer) {
				$player->hidePlayer($onlinePlayer);
			}
			$playerSession->hidePlayer = true;
			$item->setCustomName(TextFormat::GREEN.TextFormat::BOLD."Show players§r\n".TextFormat::GRAY."[".TextFormat::AQUA."Click".TextFormat::GRAY."]");
		}
		$player->getInventory()->setItemInHand($item);
	}
}
