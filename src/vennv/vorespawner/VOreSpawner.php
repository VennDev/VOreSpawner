<?php

/**
 * VJesusBucket - PocketMine plugin.
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

namespace vennv\vorespawner;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\block\tile\TileFactory;
use vennv\vapm\VapmPMMP;
use vennv\vorespawner\data\DataManager;
use vennv\vorespawner\listener\EventListener;
use vennv\vorespawner\tile\OreSpawnerTile;

final class VOreSpawner extends PluginBase {

	private static VOreSpawner $instance;

	public static function getInstance() : VOreSpawner {
		return self::$instance;
	}

	public function onLoad() : void {
		self::$instance = $this;
	}

	protected function onEnable() : void {
		VapmPMMP::init($this);

		$this->saveDefaultConfig();

		TileFactory::getInstance()->register(
			OreSpawnerTile::class, ["OreSpawnerTile", "minecraft:ore_spawner"]
		);

		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if ($command->getName() == "vorespawner") {
			if (!isset($args[0])) {
				return false;
			}

			if ($args[0] == "give") {
				if (
					!isset($args[1]) ||
					!isset($args[2]) ||
					!isset($args[3]) ||
					!isset($args[4])
				) {
					return false;
				}

				if (!is_numeric($args[3]) || !is_numeric($args[4])) {
					$sender->sendMessage("Amount or Level must be a number");
					return false;
				}

				$player = $sender->getServer()->getPlayerExact($args[1]);
				if ($player == null) {
					$sender->sendMessage("Player not found");
					return false;
				} else {
					DataManager::giveOreSpawner($player, $args[2], (int)$args[3], (int)$args[4]);
				}
			}
		}

		return true;
	}

}