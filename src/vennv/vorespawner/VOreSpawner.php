<?php

declare(strict_types=1);

namespace vennv\vorespawner;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\block\tile\TileFactory;
use pocketmine\utils\SingletonTrait;
use venndev\vosaka\VOsaka;
use vennv\vorespawner\factory\SpawnerFactory;
use vennv\vorespawner\listener\EventListener;
use vennv\vorespawner\tile\OreSpawnerTile;
use vosaka\pmmp\VOsakaPMMP;

final class VOreSpawner extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        // Init VOsaka
        VOsakaPMMP::init($this);

        $this->saveDefaultConfig();

        TileFactory::getInstance()->register(
            OreSpawnerTile::class,
            ["OreSpawnerTile", "minecraft:ore_spawner"]
        );

        $this->getServer()->getPluginManager()->registerEvents(
            new EventListener(),
            $this
        );
    }

    public function onCommand(
        CommandSender $sender,
        Command $command,
        string $label,
        array $args
    ): bool {
        if (!isset($args[0])) {
            return false;
        }

        if ($args[0] == "give") {
            $checkArgs = !isset($args[1]) || !isset($args[2]) ||
                !isset($args[3]) || !isset($args[4]);
            if ($checkArgs) {
                return false;
            }

            $validateArgs = !is_numeric($args[3]) || !is_numeric($args[4]);
            if ($validateArgs) {
                $sender->sendMessage("Amount or Level must be a number");
                return false;
            }

            $player = $sender->getServer()->getPlayerExact($args[1]);
            if ($player == null) {
                $sender->sendMessage("Player not found");
                return false;
            }

            VOsaka::spawn(SpawnerFactory::giveOreSpawner(
                $player,
                $args[2],
                (int)$args[3],
                (int)$args[4]
            ));
        }

        return true;
    }
}
