<?php

declare(strict_types=1);

namespace Nuke;

use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\world\sound\ChestCloseSound;
use pocketmine\world\sound\Sound;

class NukeTask extends task {
	
	private int $time;
	private Main $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
		$this->time = $main->getConfig()->get("CountDown");
	}
	
	public function onRun() : void {
		$i = array("run");
		foreach ($i as $ignored) {
			if ($this->time == 0) {
				foreach ($this->main->getServer()->getOnlinePlayers() as $players) {
				  if ($this->main->getServer()->isOp($players->getName())) continue;
					if ($this->main->getConfig()->get("ExplotionSound")) {
						$packet = new LevelSoundEventPacket();
						$packet->sound = LevelSoundEvent::EXPLODE;
						$packet->extraData = 0;
						$packet->position = $players->getPosition();
						$players->getNetworkSession()->sendDataPacket($packet);
						$players->kill();
					}
				}
				$message1 = str_replace("{victims}", strval(count($this->main->getServer()->getOnlinePlayers())), $this->main->getConfig()->get("VictimsMessage"));
				$this->main->getServer()->broadcastMessage($message1);
				$this->main->nuke = false;
				throw new CancelTaskException();
			}
			foreach ($this->main->getServer()->getOnlinePlayers() as $players) {
				if ($this->main->getConfig()->get("CountDownSound")) {
					$packet = new LevelSoundEventPacket();
					$packet->sound = LevelSoundEvent::BLOCK_CLICK;
					$packet->extraData = 0;
					$packet->position = $players->getPosition();
					$players->getNetworkSession()->sendDataPacket($packet);
				}
			}
			$message2 = str_replace("{time}", strval($this->time), $this->main->getConfig()->get("CountDownMessage"));
			$this->main->getServer()->broadcastTitle($message2);
			$this->main->getLogger()->info($message2);
			$this->time--;
		}
	}
}