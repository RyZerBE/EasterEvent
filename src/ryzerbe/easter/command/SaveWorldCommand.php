<?php

declare(strict_types=1);

namespace ryzerbe\easter\command;

use javamapconverter\skull\SkullChunkManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\easter\entity\EasterBunnyEntity;
use ryzerbe\easter\Loader;

class SaveWorldCommand extends Command {
    public function __construct(){
        parent::__construct("saveworld", "Save easter map to template folder", "", []);
        $this->setPermission("ryzer.admin");
        $this->setPermissionMessage(TextFormat::RED."The command is only for easter bunnies! :3");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$this->testPermission($sender)) return;

        if($sender instanceof Player) {
        	foreach ($sender->getLevel()->getEntities() as $entity) {
        		if($entity instanceof EasterBunnyEntity) {
        			$entity->flagForDespawn(); //useless to save the bunny
				}
			}
			foreach (SkullChunkManager::getInstance()->getChunks($sender->getLevelNonNull()) as $skullChunk) {
				$skullChunk->onUnload();
			}
		}
		Server::getInstance()->getDefaultLevel()->save(true);

		AsyncExecutor::submitAsyncTask(function(): void {
            exec("cp worlds/world /root/RyzerCloud/templates/Testserver/worlds/ -r");
        }, function() use ($sender): void {
            $sender->sendMessage(Loader::PREFIX."Map wurde erfolgreich gespeichert.");
            Loader::getInstance()->getLogger()->info("Map saved successfully.");
        });
    }
}