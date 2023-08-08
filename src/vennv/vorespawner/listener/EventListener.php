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

namespace vennv\vorespawner\listener;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\block\BlockBreakEvent;
use vennv\vorespawner\data\DataManager;
use vennv\vorespawner\event\VOreSpawnedEvent;
use vennv\vorespawner\tile\OreSpawnerTile;
use vennv\vapm\Promise;
use vennv\vapm\System;
use Throwable;

final class EventListener implements Listener {

	/**
	 * @var array<string, Promise>
	 */
	private static array $promises = [];

	private const RADIUS = 5; // The radius to check for spawners.

	/**
	 * @throws Throwable
	 */
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
		$origin = $event->getOrigin();
		$player = $origin->getPlayer();

		if ($player === null) {
			return;
		}

		if (!$player->isOnline()) {
			return;
		}

		$radius = self::RADIUS;

		for ($x = -$radius; $x < $radius; $x++) {
			for ($y = -$radius; $y < $radius; $y++) {
				for ($z = -$radius; $z < $radius; $z++) {
					$tile = $player->getWorld()->getTile($player->getLocation()->add($x, $y, $z));

					if (!$tile instanceof OreSpawnerTile) {
						continue;
					}

					if (isset(self::$promises[$tile->getId()])) {
						continue;
					}

					self::$promises[$tile->getId()] = Promise::c(function($resolve) use ($tile) : void {
						$resolve($tile->onUpdate());
					})->then(function () use ($tile) : void {
						unset(self::$promises[$tile->getId()]);
					});
				}
			}
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) : void {
		$player = $event->getPlayer();
		$block = $event->getBlockAgainst();

		$position = $block->getPosition();
		$world = $player->getWorld();
		$inventory = $player->getInventory();
		$itemHand = $inventory->getItemInHand();

		if (DataManager::getOreSpawnerType($itemHand) === null) {
			return;
		}

		$type = DataManager::getOreSpawnerType($itemHand);
		$level = DataManager::getOreSpawnerLevel($itemHand);

		$data = DataManager::getDataSpawner($type);

		if ($data === null) {
			return;
		}

		$dataLevel = DataManager::getSpawnerLevelData($type, $level);

		if ($dataLevel === null) {
			return;
		}

		$speed = (int)$dataLevel["speed"];

		$vectorSpawn = $position->asVector3()->add(0, 1, 0);

		$tile = new OreSpawnerTile($world, $vectorSpawn);
		$tile->setSpeed($speed);
		$tile->setType($type);
		$tile->setLevel($level);
		$tile->setBlocks($data["blocks"]);
		$tile->setId($position->getX() . ":" . $position->getY() . ":" . $position->getZ());

		// Delay to tile spawn when player place the spawner.
		System::setTimeout(function () use ($world, $tile) : void {
			$world->addTile($tile);
		}, 500);

		$eventSpawned = new VOreSpawnedEvent($player, $type, $level, $vectorSpawn);
		$eventSpawned->call();
	}

	public function onBlockBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();
		$block = $event->getBlock();

		$position = $block->getPosition();
		$world = $player->getWorld();

		$tile = $world->getTile($position->asVector3());

		if (!$tile instanceof OreSpawnerTile) {
			return;
		}

		$type = $tile->getType();
		$level = $tile->getLevel();

		$data = DataManager::getDataSpawner($type);

		if ($data === null) {
			return;
		}

		$item = DataManager::getOreSpawner($type, $level);

		if ($item !== null) {
			$event->setDrops([]);
			$event->setXpDropAmount(0);

			$player->getInventory()->addItem($item);
		} else {
			Server::getInstance()->getLogger()->error("Error while getting item from spawner, please report this error to the developer.");
		}

		$world->removeTile($tile);
	}

}