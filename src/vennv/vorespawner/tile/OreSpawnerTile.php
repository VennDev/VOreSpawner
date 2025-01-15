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

namespace vennv\vorespawner\tile;

use pocketmine\block\Air;
use pocketmine\block\tile\Spawnable;
use pocketmine\item\ItemBlock;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\world\World;
use vennv\vorespawner\utils\ItemUtil;

final class OreSpawnerTile extends Spawnable {

	protected int $ticks = 0;

	protected int $ticksGoal = 400;

	protected int $level = 0;

	protected int $speed = 0;

	/**
	 * @var array<int, string> // This is a block array, save with name of block
	 */
	protected array $blocks = [];

	protected string $type = "";

	protected string $owner;

	protected string $id = "OreSpawnerTile";

	public function getTicks() : int {
		return $this->ticks;
	}

	public function getTicksGoal() : int {
		return $this->ticksGoal;
	}

	public function setTicksGoal(int $ticksGoal) : void {
		$this->ticksGoal = $ticksGoal;
	}

	public function getLevel() : int {
		return $this->level;
	}

	public function setLevel(int $level) : void {
		$this->level = $level;
	}

	public function getSpeed() : int {
		return $this->speed;
	}

	public function setSpeed(int $speed) : void {
		$this->speed = $speed;
	}

	public function setOwner(string $owner) : void {
		$this->owner = $owner;
	}

	public function getOwner() : string {
		return $this->owner;
	}

	public function getBlocks() : array {
		return $this->blocks;
	}

	public function setBlocks(array $blocks) : void {
		$this->blocks = $blocks;
	}

	public function getType() : string {
		return $this->type;
	}

	public function setType(string $type) : void {
		$this->type = $type;
	}

	public function getId() : string {
		return $this->id;
	}

	public function setId(string $id) : void {
		$this->id = $id;
	}

	protected function initCompoundTag(CompoundTag $nbt) : void {
		$nbt->setInt("ticks", $this->ticks);
		$nbt->setInt("ticksGoal", $this->ticksGoal);
		$nbt->setInt("level", $this->level);
		$nbt->setInt("speed", $this->speed);
		$nbt->setString("owner", $this->owner);
		$nbt->setString("blocks", json_encode($this->blocks));
		$nbt->setString("type", $this->type);
		$nbt->setString("idTile", $this->id);
	}

	protected function addAdditionalSpawnData(CompoundTag $nbt) : void {
		$this->initCompoundTag($nbt);
	}

	public function readSaveData(CompoundTag $nbt) : void {
		$this->ticks = $nbt->getInt("ticks");
		$this->ticksGoal = $nbt->getInt("ticksGoal");
		$this->level = $nbt->getInt("level");
		$this->speed = $nbt->getInt("speed");
		$this->owner = $nbt->getString("owner");
		$this->blocks = json_decode($nbt->getString("blocks"), true);
		$this->type = $nbt->getString("type");
		$this->id = $nbt->getString("idTile");
	}

	protected function writeSaveData(CompoundTag $nbt) : void {
		$this->initCompoundTag($nbt);
	}

	public function onUpdate() : bool {
		if ($this->closed) return true;

		if ($this->ticks < $this->ticksGoal) {
			$this->ticks += $this->speed;
			return true;
		}

		$random = $this->blocks[array_rand($this->blocks)];
		$world = $this->getPosition()->getWorld();

		$vector = $this->getPosition()->add(0, 1, 0);
		if ($vector->getY() >= World::Y_MAX) return true;

		if (!$world->getBlock($vector) instanceof Air) return true;

		$block = ItemUtil::getItemFromString($random);

		if (!$block instanceof ItemBlock) {
			Server::getInstance()->getLogger()->warning("Invalid block in OreSpawnerTile: " . $random);
			return true;
		}

		$block = $block->getBlock();
		$world->setBlock($vector, $block);

		$this->ticks = 0;

		return false;
	}

}