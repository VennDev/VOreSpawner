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

namespace vennv\vorespawner\data;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use vennv\vorespawner\utils\ItemUtil;
use vennv\vorespawner\VOreSpawner;
use Throwable;

final class DataManager {

	public static function getConfig() : Config {
		return VOreSpawner::getInstance()->getConfig();
	}

	public static function getDataSpawner(string $type) : ?array {
		$types = self::getConfig()->get("ore_spawner_types");

		if (!isset($types[$type])) {
			return null;
		}

		return $types[$type];
	}

	public static function giveOreSpawner(Player $player, string $type, int $level = 1, int $amount = 1) : bool {
		$item = self::getOreSpawner($type, $level);

		if ($item === null) {
			Server::getInstance()->getLogger()->error("Failed to give ore spawner: $type");
			return false;
		}

		for ($i = 0; $i < $amount; $i++) {
			try {
				$player->getInventory()->addItem($item);
			} catch (Throwable $error) {
				Server::getInstance()->getLogger()->error("Failed to give ore spawner: $type" . PHP_EOL . $error->getMessage());
				return false;
			}
		}

		return true;
	}

	public static function getOreSpawner(string $type, int $level = 1) : ?Item {
		$data = self::getDataSpawner($type);

		if ($data === null) {
			return null;
		}

		$itemData = $data["item_data"];

		$item = ItemUtil::getItemFromString($itemData["item"]);

		if ($item === null) {
			return null;
		}

		$item->setCustomName($itemData["name"]);
		$lore = $itemData["lore"];
		foreach ($lore as $index => $line) {
			$lore[$index] = str_replace("%level%", "$level", $line);
		}

		$item->setLore($lore);

		$tags = [
			$item->getNamedTag()->setString("ore_spawner_type", $type),
			$item->getNamedTag()->setInt("ore_spawner_level", $level)
		];

		foreach ($tags as $tag) {
			$item->setNamedTag($tag);
		}

		return $item;
	}

	public static function getOreSpawnerType(Item $item) : ?string {
		try {
			return $item->getNamedTag()->getString("ore_spawner_type");
		} catch (Throwable $error) {
			return null;
		}
	}

	public static function getOreSpawnerLevel(Item $item) : ?int {
		try {
			return $item->getNamedTag()->getInt("ore_spawner_level");
		} catch (Throwable $error) {
			return null;
		}
	}

	public static function getSpawnerLevelData(string $type, int $level = 1) : ?array {
		$data = self::getDataSpawner($type);

		if ($data === null) {
			return null;
		}

		if (!isset($data["levels"][$level])) {
			return null;
		}

		return $data["levels"][$level];
	}

}