<?php

/**
 * VOreSpawner - PocketMine plugin.
 * Copyright (C) 2023 - 2025 VennDev
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace vennv\vorespawner\event;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class VOreSpawnedEvent extends Event implements Cancellable {

	private bool $cancelled = false;

	private Player $player;

	private string $type;

	private int $level;

	private Vector3 $vector3;

	public function __construct(Player $player, string $type, int $level, Vector3 $vector3) {
		$this->player = $player;
		$this->type = $type;
		$this->level = $level;
		$this->vector3 = $vector3;
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getType() : string {
		return $this->type;
	}

	public function getLevel() : int {
		return $this->level;
	}

	public function getVector3() : Vector3 {
		return $this->vector3;
	}

	public function isCancelled() : bool {
		return $this->cancelled;
	}

}