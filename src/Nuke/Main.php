<?php
declare(strict_types=1);

namespace Nuke;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;

class Main extends PluginBase{
	
	public bool $nuke = false;

	public function onEnable() : void {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents(new EventsListener($this), $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if ($command->getName() == "nuke") {
			if ($this->nuke == false) {
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