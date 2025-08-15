<?php

declare(strict_types=1);

namespace vennv\vorespawner\utils;

use Generator;
use Throwable;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\item\StringToItemParser;

final class ItemUtil
{

    /**
     * Converts a string representation of an item into an Item object.
     *
     * @param string $string The string representation of the item.
     * @return Item|null Returns the Item object if successful, or null if parsing fails.
     */
    public static function getItemFromString(string $string): ?Item
    {
        return StringToItemParser::getInstance()->parse($string);
    }

    /**
     * Gives a specified amount of an item to a player.
     *
     * @param Player $player The player to give the item to.
     * @param Item $item The item to give.
     * @param int $amount The amount of the item to give.
     * @return Generator<bool> Returns true if successful, false if it fails to add the item to the player's inventory.
     */
    public static function giveSpawnerItemToPlayer(Player $player, Item $item, int $amount): Generator
    {
        for ($i = 0; $i < $amount; $i++) {
            try {
                $player->getInventory()->addItem($item);
            } catch (Throwable) {
                return false;
            }
            yield;
        }
        return true;
    }
}

