<?php
declare(strict_types=1);

namespace it\cheditx\nuke;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;

class EventsListener implements Listener {
	
	private Main $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event) {
		$event->setKeepInventory(($this->main->nuke and $this->main->getConfig()->get("KeepInventory")));
	    if ($this->main->nuke) {
			$message = str_replace("{player}", $event->getPlayer()->getName(), $this->main->getConfig()->get("DeathByNukeMessage"));
		    $event->setDeathMessage($message);
		}
	}
}