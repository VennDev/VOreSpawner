# VOreSpawner
- Plugin OreSpawner for PocketMine-PMMP 5
- Giving your server exciting gameplay comes from placing ores spawn machines or so to speak, custom block types at your disposal.

# Config
```config
---

# Types of OreSpawner
ore_spawner_types:
  coal:

    # Item to spawn
    item_data:
      item: "minecraft:coal_ore"
      name: "Coal Ore"
      lore:
        - "Coal Ore"
        - "Level: %level%"

    # Block to spawn
    blocks:
      - "minecraft:stone"
      - "minecraft:coal_ore"

    # Spawn level
    levels:
      1:
        speed: 1 # Speed of spawn
      2:
        speed: 2 # Speed of spawn
      3:
        speed: 3 # Speed of spawn

...
```
