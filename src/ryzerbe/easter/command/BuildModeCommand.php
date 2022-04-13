<?php

declare(strict_types=1);

namespace ryzerbe\easter\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\player\PMMPPlayer;
use ryzerbe\easter\Loader;
use ryzerbe\easter\session\PlayerSessionManager;

class BuildModeCommand extends Command {
    public function __construct(){
        parent::__construct("buildmode", "Toggle build mode", "", []);
        $this->setPermission("ryzer.admin");
        $this->setPermissionMessage(TextFormat::RED."The command is only for easter bunnies! :3");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof PMMPPlayer || !$this->testPermission($sender)) return;

        $session = PlayerSessionManager::get($sender);
        if($session === null) return;
        $session->setBuildMode(!$session->isBuildMode());
        $sender->sendMessage(Loader::PREFIX."Build mode ist jetzt ".($session->isBuildMode() ? "aktiviert." : "deaktiviert."));
        if(!$session->isBuildMode()) {
            Server::getInstance()->dispatchCommand($sender, "saveworld");
        }
    }
}
