<?php
declare(strict_types=1);

namespace Nuke;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;

class NukeTask extends task
{

	private int $time;
	private Main $main;
	private int $totalKills = 0;
	private ?Player $sender = null;

	public function __construct(Main $main, ?Player $sender)
	{
		$this->main = $main;
		if ($sender !== null) {
			$this->sender = $sender;
		}
		$this->time = $main->getConfig()->get("CountDown");
	}

	public function onRun(): void {
		if ($this->time == 0) {
			if ($this->main->getConfig()->get("ExplosionSound")) {
				self::sendPacket(LevelSoundEvent::EXPLODE, true);
			}
			$this->main->getServer()->broadcastMessage(str_replace("{victims}", (string)$this->totalKills, $this->main->getConfig()->get("VictimsMessage")));
			$this->main->nuke = false;
			throw new CancelTaskException();
		}
		if ($this->main->getConfig()->get("CountDownSound")) {
			self::sendPacket(LevelSoundEvent::BLOCK_CLICK);
		}
		if ($this->main->getConfig()->get("GlobalNuke")) {
			$this->main->getServer()->broadcastTitle(str_replace("{time}", strval($this->time), $this->main->getConfig()->get("CountDownMessage")), stay: 10);
			$this->time--;
		} else {
			$level = $this->main->getServer()->getWorldManager()->getWorldByName($this->main->getConfig()->get("NukeWorld"));
			foreach ($level->getPlayers() as $player) {
				$player->sendTitle(str_replace("{time}", strval($this->time), $this->main->getConfig()->get("CountDownMessage")), stay: 10);
				$this->time--;
			}
		}

	}

	private function sendPacket(int $sound, bool $kill = false) {
		if (!$this->main->getConfig()->get("GlobalNuke")) {
			$level = $this->main->getServer()->getWorldManager()->getWorldByName($this->main->getConfig()->get("NukeWorld"));
			foreach ($level->getPlayers() as $player) {
				if ($kill and $this->main->getServer()->isOp($player->getName())) continue;
				$packet = new LevelSoundEventPacket();
				$packet->sound = $sound;
				$packet->extraData = 0;
				$packet->position = $player->getPosition();
				$player->getNetworkSession()->sendDataPacket($packet);
				if ($kill) {
					if ($this->sender !== null and $player->getName() === $this->sender->getName()) continue;
					$player->kill();
					$this->totalKills++;
				}
			}
		} else {
			foreach ($this->main->getServer()->getOnlinePlayers() as $player) {
				if ($kill and $this->main->getServer()->isOp($player->getName())) continue;
				$packet = new LevelSoundEventPacket();
				$packet->sound = $sound;
				$packet->extraData = 0;
				$packet->position = $player->getPosition();
				$player->getNetworkSession()->sendDataPacket($packet);
				if ($kill) {
					if ($this->sender !== null and $player->getName() === $this->sender->getName()) continue;
					$player->kill();
					$this->totalKills++;
				}
			}
		}
	}
}
