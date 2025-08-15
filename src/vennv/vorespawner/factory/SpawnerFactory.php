<?php

declare(strict_types=1);

namespace vennv\vorespawner\factory;

use Generator;
use pocketmine\Server;
use pocketmine\player\Player;
use vennv\vorespawner\data\DataManager;
use vennv\vorespawner\utils\ItemUtil;

final class SpawnerFactory
{
    public static function giveOreSpawner(
        Player $player,
        string $type,
        int $level = 1,
        int $amount = 1
    ): Generator {
        $item = DataManager::getOreSpawner($type, $level);

        if ($item === null) {
            Server::getInstance()->getLogger()->error("Failed to give ore spawner: $type");
            return false;
        }

        $result = yield from ItemUtil::giveSpawnerItemToPlayer($player, $item, $amount);
        if ($result === false) {
            Server::getInstance()->getLogger()->error("Failed to give ore spawner: $type");
            return false;
        }

        return true;
    }
}
