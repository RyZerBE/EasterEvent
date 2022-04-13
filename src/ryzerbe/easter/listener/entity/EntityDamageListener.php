<?php

declare(strict_types=1);

namespace ryzerbe\easter\listener\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EntityDamageListener implements Listener {
    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;
        $event->setCancelled();
    }
}