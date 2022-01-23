<?php
declare(strict_types=1);

namespace it\cheditx\nuke;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;

class NukeTask extends Task {

	private int $time;
	private Main $main;
	private int $totalKills = 0;
	private ?Player $sender = null;

	public function __construct(Main $main, ?Player $sender) {
		$this->main = $main;
		if ($sender !== null) {
			$this->sender = $sender;
		}
		$this->time = $main->getConfig()->get("CountDown");
	}

	public function onRun(): void {
		if ($this->time == 0) {
			if ($this->main->getConfig()->get("ExplosionSound")) {
				$this->sendPacket(LevelSoundEvent::EXPLODE, true);
			}
			$this->main->getServer()->broadcastMessage(str_replace("{victims}", (string)$this->totalKills, $this->main->getConfig()->get("VictimsMessage")));
			$this->main->nuke = false;
			throw new CancelTaskException();
		}
		if ($this->main->getConfig()->get("CountDownSound")) {
			$this->sendPacket(LevelSoundEvent::BLOCK_CLICK);
		}
		if ($this->main->getConfig()->get("GlobalNuke")) {
			$recipients = $this->main->getServer()->getOnlinePlayers();
		} else {
			$level = $this->main->getServer()->getWorldManager()->getWorldByName($this->main->getConfig()->get("NukeWorld"));
			$recipients = $level !== null ? $level->getPlayers() : [];
		}
		$this->main->getServer()->broadcastTitle(str_replace("{time}", strval($this->time), $this->main->getConfig()->get("CountDownMessage")), stay: 10, recipients: $recipients);
		$this->time--;
	}

	private function sendPacket(int $sound, bool $kill = false) {
		if (!$this->main->getConfig()->get("GlobalNuke")) {
			$level = $this->main->getServer()->getWorldManager()->getWorldByName($this->main->getConfig()->get("NukeWorld"));
			$players = $level !== null ? $level->getPlayers() : [];
		} else {
			$players = $this->main->getServer()->getOnlinePlayers();
		}
		foreach ($players as $player) {
			if ($player === $this->sender or $this->main->getServer()->isOp($player->getName())) continue;
			$packet = new LevelSoundEventPacket();
			$packet->sound = $sound;
			$packet->extraData = 0;
			$packet->position = $player->getPosition();
			$player->getNetworkSession()->sendDataPacket($packet);
			if ($kill) {
				$player->kill();
				$this->totalKills++;
			}
		}
	}
}
