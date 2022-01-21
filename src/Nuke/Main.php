<?php
declare(strict_types=1);

namespace Nuke;

use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\player\Player;

class Main extends PluginBase{
	
	public bool $nuke = false;

	public function onEnable() : void {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents(new EventsListener($this), $this);
		$worldName = $this->getConfig()->get("NukeWorld");
		if ($this->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
			$this->getServer()->getWorldManager()->loadWorld($worldName);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if ($command->getName() == "nuke") {
			if (!$this->nuke) {
				$this->nuke = true;
				$sender->sendMessage($this->getConfig()->get("NukeActivated"));
				$this->getScheduler()->scheduleRepeatingTask(new NukeTask($this, ($sender instanceof Player ? $sender : null)), 20);
				return false;
			} else {
				$sender->sendMessage($this->getConfig()->get("NukeAlreadyActivated"));
			}
		}
		return false;
	}
}