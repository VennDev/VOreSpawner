<?php

declare(strict_types=1);

namespace vennv\vorespawner\listener;

use Generator;
use pocketmine\Server;
use pocketmine\block\Air;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\block\BlockBreakEvent;
use venndev\vosaka\time\Sleep;
use venndev\vosaka\VOsaka;
use vennv\vorespawner\data\DataManager;
use vennv\vorespawner\event\VOreSpawnedEvent;
use vennv\vorespawner\tile\OreSpawnerTile;
use vennv\vorespawner\VOreSpawner;

final class EventListener implements Listener
{
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $origin = $event->getOrigin();
        $player = $origin->getPlayer();

        if ($player === null) return;
        if (!$player->isOnline()) return;

        VOsaka::spawn($this->processSpawnersNearby($player, DataManager::getConfig()->getNested('radius', 5)));
    }

    private function processSpawnersNearby(Player $player, int $radius): Generator
    {
        for ($x = -$radius; $x < $radius; $x++) {
            for ($y = -$radius; $y < $radius; $y++) {
                for ($z = -$radius; $z < $radius; $z++) {
                    $tile = $player->getWorld()->getTile($player->getLocation()->add($x, $y, $z));

                    if (!$tile instanceof OreSpawnerTile) {
                        continue;
                    }

                    $tile->onUpdate();

                    yield;
                }
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $itemHand = $event->getItem();
        $block = $event->getBlockAgainst();

        $sendError = function ($event, $player): void {
            $player->sendMessage(DataManager::getConfig()->getNested('messages.wrong_position'));
            $event->cancel();
        };

        if (DataManager::getOreSpawnerType($itemHand) === null) return;

        $type = DataManager::getOreSpawnerType($itemHand);
        $level = DataManager::getOreSpawnerLevel($itemHand);

        $data = DataManager::getDataSpawner($type);
        $dataLevel = DataManager::getSpawnerLevelData($type, $level);
        $updateInterval = DataManager::getUpdateInterval();


        if ($data === null || $dataLevel === null) return;

        $world = $player->getWorld();
        $vector3 = $block->getPosition()->floor();
        $vector3->y += 1;

        $samples = [
            new Vector3($vector3->getX(), $vector3->getY() - 2, $vector3->getZ()),
            new Vector3($vector3->getX(), $vector3->getY() - 1, $vector3->getZ()),
            new Vector3($vector3->getX(), $vector3->getY(), $vector3->getZ())
        ];

        // Check if the player is placing the spawner in the right position.
        foreach ($samples as $sample) {
            if ($world->getTile($sample) !== null) {
                $sendError($event, $player);
                return;
            }
        }

        $blockAt = $world->getBlock($vector3);
        if (!$blockAt instanceof Air) {
            $sendError($event, $player);
            return;
        }

        $speed = (int)$dataLevel["speed"];

        $tile = new OreSpawnerTile($world, $vector3);
        $tile->setSpeed($speed);
        $tile->setType($type);
        $tile->setOwner($player->getName());
        $tile->setLevel($level);
        $tile->setTicksGoal($updateInterval);
        $tile->setBlocks($data["blocks"]);
        $tile->setId($vector3->getX() . ":" . $vector3->getY() . ":" . $vector3->getZ());

        $eventSpawned = new VOreSpawnedEvent($player, $type, $level, $vector3);
        $eventSpawned->call();

        // delay the spawn of the ore.
        VOsaka::spawn(function () use ($world, $tile, $eventSpawned): Generator {
            yield Sleep::new(0.6);

            if ($eventSpawned->isCancelled()) {
                return;
            }

            $world->addTile($tile);
        });
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        $position = $block->getPosition();
        $world = $player->getWorld();

        $tile = $world->getTile($position->asVector3());

        if (!$tile instanceof OreSpawnerTile) return;

        $owner = $tile->getOwner();
        if ($owner !== $player->getName()) {
            $player->sendMessage(DataManager::getConfig()->getNested('messages.no_permission'));
            $event->cancel();
            return;
        }

        $type = $tile->getType();
        $level = $tile->getLevel();
        $data = DataManager::getDataSpawner($type);

        if ($data === null) return;

        $item = DataManager::getOreSpawner($type, $level);
        if ($item !== null) {
            $event->setDrops([]);
            $event->setXpDropAmount(0);
            $player->getInventory()->addItem($item);
        } else {
            Server::getInstance()->getLogger()->error(
                "Error while getting item from spawner, please report this error to the developer."
            );
        }

        $world->removeTile($tile);
    }
}
