<?php

declare(strict_types=1);

namespace Nuke;

use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\utils\Config;

class Main extends PluginBase{

	public function onEnable() : void{
		$this->getLogger()->info("§f[§4NUKE§f] > §2ON§f!");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents(new EventsListener($this), $this);
	}

	public function onDisable() : void{
		$this->getLogger()->info("§f[§4NUKE§f] > §cOFF§f!");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "nuke":
				$sender->sendMessage("§f[§4NUKE§f] > §aNuke activated!");
				$this->getScheduler()->scheduleRepeatingTask(new NukeTask($this), 20);
	            return false;
		}
	}
}