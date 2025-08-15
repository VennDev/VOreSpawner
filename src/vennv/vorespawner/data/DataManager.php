<?php

declare(strict_types=1);

namespace vennv\vorespawner\data;

use Throwable;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\Config;
use vennv\vorespawner\utils\ItemUtil;
use vennv\vorespawner\VOreSpawner;

final class DataManager
{
    public static function getConfig(): Config
    {
        return VOreSpawner::getInstance()->getConfig();
    }

    public static function getUpdateInterval(): int
    {
        return self::getConfig()->get("update_interval", 400) * 20;
    }

    public static function getDataSpawner(string $type): ?array
    {
        $types = self::getConfig()->get("ore_spawner_types");
        if (!isset($types[$type])) return null;
        return $types[$type];
    }

    public static function getOreSpawner(string $type, int $level = 1): ?Item
    {
        $data = self::getDataSpawner($type);
        if ($data === null) return null;

        $itemData = $data["item_data"];
        $item = ItemUtil::getItemFromString($itemData["item"]);

        if ($item === null) return null;

        $item->setCustomName($itemData["name"]);
        $lore = $itemData["lore"];
        foreach ($lore as $index => $line) {
            $lore[$index] = str_replace("%level%", "$level", $line);
        }

        $item->setLore($lore);
        $item->getNamedTag()->setString("ore_spawner_type", $type);
        $item->getNamedTag()->setInt("ore_spawner_level", $level);

        return $item;
    }

    public static function getOreSpawnerType(Item $item): ?string
    {
        try {
            return $item->getNamedTag()->getString("ore_spawner_type");
        } catch (Throwable) {
            return null;
        }
    }

    public static function getOreSpawnerLevel(Item $item): ?int
    {
        try {
            return $item->getNamedTag()->getInt("ore_spawner_level");
        } catch (Throwable) {
            Server::getInstance()->getLogger()->error(
                "Failed to get ore spawner level from item: " . $item->getName()
            );
            return null;
        }
    }

    public static function getSpawnerLevelData(string $type, int $level = 1): ?array
    {
        $data = self::getDataSpawner($type);
        if ($data === null) return null;
        if (!isset($data["levels"][$level])) return null;
        return $data["levels"][$level];
    }
}
