<?php
declare(strict_types=1);
namespace ryzerbe\easter\entity;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\language\LanguageProvider;
use ryzerbe\core\util\SkinUtils;


class EasterBunnyEntity extends Human implements ChunkLoader
{
	/**
	 * TopShieldEntity constructor.
	 * @param Level $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		$this->skin = new Skin(uniqid(), SkinUtils::fromImage("/root/RyzerCloud/data/NPC/EasterBunny.png"));
		parent::__construct($level, $nbt);
		$this->getLevel()->loadChunk($this->x >> 4, $this->z >> 4);
		$level->registerChunkLoader($this, $this->x >> 4, $this->z >> 4, true);
	}

	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void{
		$source->setCancelled();
		if($source instanceof EntityDamageByEntityEvent) {
			$player = $source->getDamager();
			if($player instanceof Player) {
				$form = new SimpleForm(function (Player $player, $data): void{});
				$form->setTitle(TextFormat::RED.TextFormat::BOLD."EASTER BUNNY");
				$form->setContent(LanguageProvider::getMessageContainer("easter-bunny-form-content", $player));
				$form->sendToPlayer($player);
			}
		}
		parent::attack($source);
	}

	/**
	 * @inheritDoc
	 */
	public function getLoaderId(): int{
		return spl_object_id($this);
	}

	/**
	 * @inheritDoc
	 */
	public function isLoaderActive(): bool{
		return !$this->isClosed();
	}

	/**
	 * @inheritDoc
	 */
	public function onChunkChanged(Chunk $chunk){
	}

	/**
	 * @inheritDoc
	 */
	public function onChunkLoaded(Chunk $chunk){
	}

	/**
	 * @inheritDoc
	 */
	public function onChunkUnloaded(Chunk $chunk){
	}

	/**
	 * @inheritDoc
	 */
	public function onChunkPopulated(Chunk $chunk){
	}

	/**
	 * @inheritDoc
	 */
	public function onBlockChanged(Vector3 $block){
	}

	/**
	 * @param int $currentTick
	 * @return bool
	 */
	public function onUpdate(int $currentTick): bool{
		/* @var int $maxDistance The distance the entity needed to be within */
		/** @var Entity $entity The entity within the radius */
		foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->expandedCopy(5, 5, 5)) as $viewer){
			if($viewer instanceof Player){
				$dist = $this->distanceSquared($viewer);
				$dir = $viewer->subtract($this);
				$dir = $dist <= 0 ? $dir : $dir->divide($dist);

				$yaw = rad2deg(atan2(-$dir->getX(), $dir->getZ()));
				$pitch = rad2deg(atan(-$dir->getY()));

				$this->setRotation($this->yaw, $this->pitch);

				$pk = new MovePlayerPacket();
				$pk->entityRuntimeId = $this->getId();
				$pk->position = $this->getOffsetPosition($this->asVector3());
				$pk->yaw = $yaw;
				$pk->headYaw = $yaw;
				$pk->pitch = $pitch;
				$viewer->dataPacket($pk);
			}
		}
		return true;
	}
}
