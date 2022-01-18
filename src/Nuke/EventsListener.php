<?php

declare(strict_types=1);

namespace Nuke;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class EventsListener implements Listener {
	
	private Main $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event) {
		$event->setKeepInventory(true);
		$message = str_replace("{player}", $event->getPlayer()->getName(), $this->main->getConfig()->get("DeathByNukeMessage"));
		$event->setDeathMessage($message);
	}
}