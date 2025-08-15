<img src="https://static.wikia.nocookie.net/minecraft_gamepedia/images/3/35/Caves_%26_Cliffs_Ores.png" alt="ReallyCheat-Premium" height="200" width="400" />

# VOreSpawner
- Plugin OreSpawner for PocketMine-PMMP 5
- This is improved upgrade from an old project of mine. [Here](https://github.com/VennDev/OreSpawner)
- Giving your server exciting gameplay comes from placing ores spawn machines or so to speak, custom block types at your disposal.

# Commands
```
/vorespawner or /vos - give <player> <type> <level> <amount>
```

# Config
```yaml
---

# Radius of the spawner
radius: 5 # Radius in blocks around the spawner where it can operate

# Update interval in seconds (will be converted to ticks internally: 1 second = 20 ticks)
update_interval: 20 # Spawner action interval in seconds

# Messages
messages:
  wrong_position: "You can't place the Spawner here!"
  no_permission: "You do not own this OreSpawner to be able to break it!"

# Types of OreSpawner
ore_spawner_types:
  coal:
    # This is the item that you will hold in your hand to represent the Spawner. In short, the item in hand to pose as the Spawner.
    item_data:
      item: "minecraft:coal_ore"
      name: "Coal Ore"
      lore:
        - "Coal Ore"
        - "Level: %level%"

    # Blocks to spawn
    blocks:
      - "minecraft:stone"
      - "minecraft:coal_ore"

    # Spawn levels and their respective speeds
    # Speed determines how quickly the spawner operates:
    # - Each level increases the speed of ticking.
    # - Speed affects the time taken for the spawner to perform its action:
    #     Real-time (seconds) = (default-interval / speed) i..e  20 / 2 = 10 seconds
    levels:
      1:
        speed: 1 # Default speed, uses full interval time.
      2:
        speed: 2 # Halves the time required.
      3:
        speed: 3 # Reduces time to one-third of the default interval.

  diamond:
    item_data:
      item: "minecraft:light_blue_glazed_terracotta"
      name: "Diamond Ore"
      lore:
        - "Diamond Ore"
        - "Level: %level%"

    # Blocks to spawn
    blocks:
      - "minecraft:diamond_ore"
    levels:
      1:
        speed: 1
      2:
        speed: 2
      3:
        speed: 3 
```

# Credits
- Email: pnam5005@gmail.com
- Paypal: lifeboat909@gmail.com
