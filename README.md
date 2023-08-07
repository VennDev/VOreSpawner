# VOreSpawner
- Plugin OreSpawner for PocketMine-PMMP 5
- This is improved upgrade from an old project of mine. [Here](https://github.com/VennDev/OreSpawner)
- Giving your server exciting gameplay comes from placing ores spawn machines or so to speak, custom block types at your disposal.

# How to install it ?
- You should install LibVapmPMMP here: [LibVapmPMMP](https://poggit.pmmp.io/ci/VennDev/LibVapmPMMP/LibVapmPMMP)

# Commands
```
/vorespawner or /vos - give <player> <type> <level> <amount>
```

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

# Credits
- Email: pnam5005@gmail.com
- Paypal: lifeboat909@gmail.com
