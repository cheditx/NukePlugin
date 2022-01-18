<?php

declare(strict_types=1);

namespace Nuke;

use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class NukeTask extends task {
	
	private int $time;
	private Main $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
		$this->time = $main->getConfig()->get("CountDown");
	}
	
	public function onRun() : void {
		$i = array("run");
		foreach ($i as $get) {
			if ($this->time == 0) {
				foreach ($this->main->getServer()->getOnlinePlayers() as $players) {
				  if ($this->main->getServer()->isOp($players->getName())) continue;
				  $players->kill();
				}
				$message1 = str_replace("{victims}", strval(count($this->main->getServer()->getOnlinePlayers())), $this->main->getConfig()->get("VictimsMessage"));
				$this->main->getServer()->broadcastMessage($message1);
				throw new CancelTaskException();
			}
			$message2 = str_replace("{time}", strval($this->time), $this->main->getConfig()->get("CountDownMessage"));
			$this->main->getServer()->broadcastTitle($message2);
			$this->main->getLogger()->info($message2);
			$this->time--;
		}
	}
}