<?php

declare(strict_types=1);

namespace Nuke;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;

class EventsListener implements Listener {
	
	private Main $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event) {
	    if ($this->main->nuke) {
			if ($this->main->getConfig()->get("KeepInventory")) {
				$event->setKeepInventory(true);
			}
			$message = str_replace("{player}", $event->getPlayer()->getName(), $this->main->getConfig()->get("DeathByNukeMessage"));
		    $event->setDeathMessage($message);
		} else {
			$event->setKeepInventory(false);
		}
	}
}