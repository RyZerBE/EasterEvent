<?php

declare(strict_types=1);

namespace ryzerbe\easter\session;

use mysqli;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\LocationUtils;
use ryzerbe\easter\Loader;
use ryzerbe\easter\manager\EasterEggManager;

class PlayerSession {
	protected bool $eggMode = false;
    protected bool $buildMode = false;

	/** @var Vector3[]  */
	protected array $found_eggs = [];

    protected array $checkpoints = [];

	public function __construct(protected PMMPPlayer $player){
		$player->setImmobile();
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
			$player->setImmobile(false);
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

        if(count($this->found_eggs) >= count(EasterEggManager::getInstance()->getEasterEggLocations())) {
            //TODO: Reward
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
		$location = LocationUtils::toString($player->asLocation());
		AsyncExecutor::submitMySQLAsyncTask("Easter", function (mysqli $mysqli) use ($playerName, $location, $eggs): void{
			$mysqli->query("INSERT INTO `data`(`player`, `position`, `eggs`) VALUES ('$playerName', '$location', '$eggs') ON DUPLICATE KEY UPDATE position='$location',eggs='$eggs'");
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

    public function addCheckpoint(Vector3 $vector3): void {
        $this->checkpoints[] = Level::blockHash($vector3->x, $vector3->y, $vector3->z);
    }
}
