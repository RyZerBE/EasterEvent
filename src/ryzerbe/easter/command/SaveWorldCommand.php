<?php

declare(strict_types=1);

namespace ryzerbe\easter\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\easter\Loader;

class SaveWorldCommand extends Command {
    public function __construct(){
        parent::__construct("saveworld", "Save easter map to template folder", "", []);
        $this->setPermission("ryzer.admin");
        $this->setPermissionMessage(TextFormat::RED."The command is only for easter bunnies! :3");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$this->testPermission($sender)) return;

        Server::getInstance()->getDefaultLevel()->save(true);
        AsyncExecutor::submitAsyncTask(function(): void {
            exec("cp worlds/world /root/RyzerCloud/templates/Testserver/worlds/ -r");
        }, function() use ($sender): void {
            $sender->sendMessage(Loader::PREFIX."Map wurde erfolgreich gespeichert.");
            Loader::getInstance()->getLogger()->info("Map saved successfully.");
        });
    }
}