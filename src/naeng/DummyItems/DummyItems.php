<?php

namespace naeng\DummyItems;

use customiesdevs\customies\item\component\AllowOffHandComponent;
use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\component\BlockPlacerComponent;
use customiesdevs\customies\item\component\CanDestroyInCreativeComponent;
use customiesdevs\customies\item\component\ChargeableComponent;
use customiesdevs\customies\item\component\CooldownComponent;
use customiesdevs\customies\item\component\DiggerComponent;
use customiesdevs\customies\item\component\DurabilityComponent;
use customiesdevs\customies\item\component\FoilComponent;
use customiesdevs\customies\item\component\FoodComponent;
use customiesdevs\customies\item\component\FuelComponent;
use customiesdevs\customies\item\component\InteractButtonComponent;
use customiesdevs\customies\item\component\KnockbackResistanceComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\CustomiesItemFactory;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use Symfony\Component\Filesystem\Path;

class DummyItems extends PluginBase{

    use SingletonTrait;

    protected array $info = [];

    protected array $blocks = [];

    public function onLoad() : void{
        self::setInstance($this);
    }

    public function getInfo(int $typeId) : array{
        return $this->info[$typeId];
    }

    public function getBlocks() : array{
        return $this->blocks;
    }

    public function onEnable() : void{
        $list = [];
        foreach(VanillaBlocks::getAll() as $block){
            $list[] = GlobalBlockStateHandlers::getSerializer()->serialize($block->getStateId())->getName();
        }
        $this->blocks = $list;
        $factory = CustomiesItemFactory::getInstance();
        $inv = CreativeInventory::getInstance();
        foreach((new Config(Path::join($this->getServer()->getDataPath(), "dummy_items.yml"), Config::YAML))->getAll() as $identifier => $info){
            $this->info[$info["type_id"]] = $info;
            $factory->registerItem(DummyItem::class, $identifier, $info["custom_name"], $info["type_id"]);
            if(isset($info["lore"])){
                $i = $factory->get($identifier);
                $inv->remove($i);
                $i->setLore($info["lore"]);
                $inv->add($i);
            }
        }

    }

}