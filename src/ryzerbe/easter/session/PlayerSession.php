<?php

declare(strict_types=1);

namespace ryzerbe\easter\session;

use mysqli;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\customitem\CustomItemManager;
use ryzerbe\core\util\discord\DiscordMessage;
use ryzerbe\core\util\LocationUtils;
use ryzerbe\easter\item\BackToCheckPointItem;
use ryzerbe\easter\item\BackToSpawnItem;
use ryzerbe\easter\item\BackToTowerItem;
use ryzerbe\easter\item\HidePlayerItem;
use ryzerbe\easter\Loader;
use ryzerbe\easter\manager\EasterEggManager;

class PlayerSession {
	protected bool $eggMode = false;
    protected bool $buildMode = false;
    protected bool $towerReached = false;
    public bool $hidePlayer = false;

	/** @var Vector3[]  */
	protected array $found_eggs = [];

    protected array $checkpoints = [];

	public function __construct(protected PMMPPlayer $player){
		$player->setImmobile();
		$inventory = $player->getInventory();
		$inventory->clearAll();
		$this->load();
	}

	public function load(): void{
		$player = $this->getPlayer();
		$playerName = $this->getPlayer()->getName();
		AsyncExecutor::submitMySQLAsyncTask("Easter", function (mysqli $mysqli) use ($playerName): ?array{
			$res = $mysqli->query("SELECT * FROM data WHERE player='$playerName'");
			if($res->num_rows <= 0) return null;

			if($data = $res->fetch_assoc()) return $data;

			return null;
		}, function (Server $server, ?array $result) use ($player): void{
			if(!$player->isConnected()) return;
			$playerSession = PlayerSessionManager::get($player);
			if($playerSession === null) return;

			if($result === null) {
				$player->teleport($server->getDefaultLevel()->getSafeSpawn());
				$player->setImmobile(false);
				return;
			}

			$player->teleport(LocationUtils::fromString($result["position"]));
			$playerSession->found_eggs = json_decode($result["eggs"]);
			$playerSession->checkpoints = json_decode($result["checkpoints"]);
			$playerSession->towerReached = boolval($result["reached_tower"]);
			$player->setImmobile(false);

			$inventory = $player->getInventory();
			$inventory->setContents([
				4 => (!$playerSession->towerReached) ? CustomItemManager::getInstance()->getCustomItemByClass(BackToCheckPointItem::class)->getItem() : CustomItemManager::getInstance()->getCustomItemByClass(BackToTowerItem::class)->getItem(),
				8 => CustomItemManager::getInstance()->getCustomItemByClass(BackToSpawnItem::class)->getItem(),
				0 => CustomItemManager::getInstance()->getCustomItemByClass(HidePlayerItem::class)->getItem()
			]);
		});
	}

	public function isEggMode(): bool{
		return $this->eggMode;
	}

    public function isBuildMode(): bool{
        return $this->buildMode;
    }

    public function setBuildMode(bool $buildMode): void{
        $this->buildMode = $buildMode;
    }

	public function foundEasterEgg(Vector3 $vector3): void{
		$this->found_eggs[] = $vector3->__toString();
		$this->getPlayer()->getRyZerPlayer()->addCoins(EasterEggManager::getInstance()->coinsPerHead, false, true);
		$this->getPlayer()->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("egg-found", $this->getPlayer()));
		$this->getPlayer()->playSound("block.turtle_egg.crack");

		$max = count(EasterEggManager::getInstance()->getEasterEggLocations());
        if(count($this->found_eggs) >= $max) {
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            	$onlinePlayer->sendMessage("\n\n\n".Loader::PREFIX.LanguageProvider::getMessageContainer("easter-all-eggs-found-broadcast", $onlinePlayer, ["#max" => $max, "#player" => $this->getPlayer()->getName()]));
			}

			$discordMessage = new DiscordMessage("https://discord.com/api/webhooks/964845709750861834/pyEGcVZongx7aiQXfb4_Js1wa3zIOkaK0ZZ6rH6MvNBCR5erbU7mdjXnE98yx9UZf243");
			$discordMessage->setMessage($this->getPlayer()->getName()." hat alle Ostereier gefunden");
			$discordMessage->send();

            $playerName = $this->getPlayer()->getName();
            AsyncExecutor::submitMySQLAsyncTask("Lobby", function (mysqli $mysqli) use ($playerName): void{
            	$mysqli->query("UPDATE `LottoTickets` SET tickets=tickets+5 WHERE playername='$playerName'");
			}, function (Server $server, $result) use ($playerName): void{
            	$player = $server->getPlayerExact($playerName);
            	if($player === null) return;

            	$player->sendMessage(Loader::PREFIX.TextFormat::GRAY."Du hast ".TextFormat::YELLOW."5x Lottotickets ".TextFormat::GREEN."geschenkt bekommen");
			});
        }
	}

	public function alreadyFound(Vector3 $vector3): bool{
		return in_array($vector3->__toString(), $this->found_eggs);
	}

	public function toggleEggMode(): bool{
		$this->eggMode = !$this->eggMode;
		$this->getPlayer()->sendMessage(Loader::PREFIX."Der Eggmode wurde ".($this->eggMode ? TextFormat::GREEN.TextFormat::BOLD."aktiviert" : TextFormat::RED.TextFormat::BOLD."deaktiviert"));
		return $this->eggMode;
	}

	public function saveSession(){
		$player = $this->getPlayer();
		$playerName = $this->getPlayer()->getName();
		$eggs = json_encode($this->found_eggs);
		$checkPoints = json_encode($this->checkpoints);
		$location = LocationUtils::toString($player->asLocation());
		$reached_tower = intval($this->hasTowerFinished());
		AsyncExecutor::submitMySQLAsyncTask("Easter", function (mysqli $mysqli) use ($playerName, $location, $eggs, $checkPoints, $reached_tower): void{
			$mysqli->query("INSERT INTO `data`(`player`, `position`, `eggs`, `checkpoints`, `reached_tower`) VALUES ('$playerName', '$location', '$eggs', '$checkPoints', '$reached_tower') ON DUPLICATE KEY UPDATE position='$location',eggs='$eggs',checkpoints='$checkPoints',reached_tower='$reached_tower'");
		});
	}

	public function getPlayer(): PMMPPlayer{
		return $this->player;
	}

	public function onUpdate(int $currentTick): void{
		$maxEggCount = count(EasterEggManager::getInstance()->getEasterEggLocations());
		$foundCount = count($this->found_eggs);
		$haveToFind = $maxEggCount - $foundCount;
		if($foundCount >= $maxEggCount) {
			$this->getPlayer()->sendActionBarMessage(LanguageProvider::getMessageContainer("all-eggs-found", $this->getPlayer(), ["#max" => $maxEggCount]));
		}else {
			$this->getPlayer()->sendActionBarMessage(LanguageProvider::getMessageContainer("egg-count-info", $this->getPlayer(), ["#found" => $foundCount, "#max" => $maxEggCount, "#diff" => $haveToFind]));
		}
	}

    public function hasCheckpoint(Vector3 $vector3): bool {
        return in_array(Level::blockHash($vector3->x, $vector3->y, $vector3->z), $this->checkpoints);
    }

	public function getLastCheckPoint(): ?Vector3{
		if(count($this->checkpoints) <= 0) return null;

		Level::getBlockXYZ($this->checkpoints[count($this->checkpoints) - 1], $x, $y, $z);
		return new Vector3($x, $y, $z);
    }

    public function addCheckpoint(Vector3 $vector3): void {
        $this->checkpoints[] = Level::blockHash($vector3->x, $vector3->y, $vector3->z);
    }

	public function finishTower(): void{
		foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
			$onlinePlayer->sendMessage("\n\n\n".Loader::PREFIX.LanguageProvider::getMessageContainer("easter-tower-reached", $onlinePlayer, ["#player" => $this->getPlayer()->getName()]));
		}

		$discordMessage = new DiscordMessage("https://discord.com/api/webhooks/964845709750861834/pyEGcVZongx7aiQXfb4_Js1wa3zIOkaK0ZZ6rH6MvNBCR5erbU7mdjXnE98yx9UZf243");
		$discordMessage->setMessage($this->getPlayer()->getName()." hat den Tower geschafft");
		$discordMessage->send();

		$this->getPlayer()->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("tower-reach-info", $this->getPlayer()));
		$this->getPlayer()->playSound("random.levelup");
		$this->getPlayer()->getRyZerPlayer()->addCoins(50000, false, true);
		$this->towerReached = true;
		$inventory = $this->getPlayer()->getInventory();
		$inventory->setContents([
			4 => CustomItemManager::getInstance()->getCustomItemByClass(BackToTowerItem::class)->getItem(),
			8 => CustomItemManager::getInstance()->getCustomItemByClass(BackToSpawnItem::class)->getItem(),
			0 => CustomItemManager::getInstance()->getCustomItemByClass(HidePlayerItem::class)->getItem()
		]);
	}

	public function hasTowerFinished(): bool{
		return $this->towerReached;
	}
}
