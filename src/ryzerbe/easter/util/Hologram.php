<?php

declare(strict_types=1);

namespace ryzerbe\easter\util;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;
use pocketmine\Server;

class Hologram {
    public function __construct(
        public Vector3 $vector3,
        public string $text,
        bool $onlyInRadius = true
    ){
        $hologram = new class(Server::getInstance()->getDefaultLevel(), Entity::createBaseNBT($this->vector3)) extends Entity implements ChunkLoader {
            public const NETWORK_ID = EntityIds::SNOWBALL;

            public $width = 0.000001;
            public $height = 0.00001;

            public bool $onlyInRadius = true;

            public function move(float $dx, float $dy, float $dz): void{}
            public function onUpdate(int $currentTick): bool{
                if(!$this->onlyInRadius){
                    $this->spawnToAll();
                    return false;
                }
                foreach(Server::getInstance()->getOnlinePlayers() as $player) {
                    if($player->distanceSquared($this) > 64) {
                        $this->despawnFrom($player);
                        continue;
                    }
                    $this->spawnTo($player);
                }
                return true;
            }
            public function getLoaderId(): int{return spl_object_id($this);}
            public function isLoaderActive(): bool{return true;}
            public function onChunkChanged(Chunk $chunk){}
            public function onChunkLoaded(Chunk $chunk){}
            public function onChunkUnloaded(Chunk $chunk){}
            public function onChunkPopulated(Chunk $chunk){}
            public function onBlockChanged(Vector3 $block){}
            public function canSaveWithChunk(): bool{return false;}
        };
        $hologram->onlyInRadius = $onlyInRadius;
        $hologram->setNameTagAlwaysVisible();
        $hologram->setNameTagVisible();
        $hologram->setNameTag($this->text);
        $hologram->setScale(0.000000000000000001);
        Server::getInstance()->getDefaultLevel()->registerChunkLoader($hologram, $this->vector3->x >> 4, $this->vector3->z >> 4);
    }
}