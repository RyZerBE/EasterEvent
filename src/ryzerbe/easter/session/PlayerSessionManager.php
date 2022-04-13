<?php

declare(strict_types=1);

namespace ryzerbe\easter\session;

use pocketmine\Player;
use ryzerbe\core\player\PMMPPlayer;

class PlayerSessionManager {

	/** @var PlayerSession[]  */
	public static array $sessions = [];

	public static function get(PMMPPlayer|string $player): ?PlayerSession{
		if($player instanceof PMMPPlayer) $player = $player->getName();

		return self::$sessions[$player] ?? null;
	}

	public static function createPlayerSession(PMMPPlayer $player){
		self::$sessions[$player->getName()] = new PlayerSession($player);
	}

	public static function deletePlayerSession(PlayerSession|Player $session, bool $saveSession = true){
		if($session instanceof Player) {
			$session = self::get($session);
			if($session === null) return;
		}
		$session->saveSession();
		unset(self::$sessions[$session->getPlayer()->getName()]);
	}

	/**
	 * Function getSessions
	 * @return array
	 */
	public static function getSessions(): array{
		return self::$sessions;
	}
}
