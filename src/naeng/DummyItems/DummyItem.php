<?php

namespace naeng\DummyItems;

use customiesdevs\customies\item\component\AllowOffHandComponent;
use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\component\BlockPlacerComponent;
use customiesdevs\customies\item\component\CanDestroyInCreativeComponent;
use customiesdevs\customies\item\component\CooldownComponent;
use customiesdevs\customies\item\component\DiggerComponent;
use customiesdevs\customies\item\component\DurabilityComponent;
use customiesdevs\customies\item\component\FoilComponent;
use customiesdevs\customies\item\component\FoodComponent;
use customiesdevs\customies\item\component\FuelComponent;
use customiesdevs\customies\item\component\InteractButtonComponent;
use customiesdevs\customies\item\component\KnockbackResistanceComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

class DummyItem extends Item implements ItemComponents{

    use ItemComponentsTrait;

    private int $maxStackSize = 64;

    public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
        parent::__construct($identifier, $name);
        $creativeInfo = new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE);
        $info = DummyItems::getInstance()->getInfo($this->getTypeId());
        $this->initComponent($info["texture"], $creativeInfo);
        $this->setupRenderOffsets(($info["render_offsets"]["width"] ?? 16), ($info["render_offsets"]["height"] ?? 16), ($info["render_offsets"]["hand_equipped"] ?? false));
        if(isset($info["off_hand"])){
            $this->addComponent(new AllowOffHandComponent($info["off_hand"]));
        }
        if(isset($info["armor_component"])){
            $this->addComponent(new ArmorComponent($info["armor_component"]["protection"], $info["armor_component"]["texture_type"]));
        }
        if(isset($info["block_placer"])){
            $this->addComponent(new BlockPlacerComponent($info["block_placer"]["block_identifier"], $info["block_placer"]["use_block_description"] ?? false));
        }
        if(isset($info["canDestroyInCreative"])){
            $this->addComponent(new CanDestroyInCreativeComponent($info["canDestroyInCreative"]));
        }
        /*
        if(isset($info["chargeable"])){
            $this->addComponent(new ChargeableComponent(floatval($info["chargeable"])));
        }
        BUG : 사용 시 패킷 문제로 서버 접속 안 되는 버그 있음
        */
        if(isset($info["cooldown"])){
            $this->addComponent(new CooldownComponent($info["cooldown"]["category"], $info["cooldown"]["duration"]));
        }
        if(isset($info["digger"])){
            $blocks = $info["digger"];
        }
        if(isset($blocks)){
            $diggerComponent = new DiggerComponent();
            (\Closure::bind(function() use($blocks){
                foreach($blocks as $block => $speed){
                    $this->destroySpeeds[] = [
                        "block" => ["name" => $block],
                        "speed" => $speed
                    ];
                }
            }, $diggerComponent, $diggerComponent::class))();
            $this->addComponent($diggerComponent);
        }
        if(isset($info["max_durability"])){
            $this->addComponent(new DurabilityComponent($info["max_durability"]));
        }
        if(isset($info["foil"])){
            $this->addComponent(new FoilComponent($info["foil"]));
        }
        if(isset($info["can_always_eat"])){
            $this->addComponent(new FoodComponent($info["can_always_eat"]));
        }
        if(isset($info["fuel"])){
            $this->addComponent(new FuelComponent(floatval($info["fuel"])));
        }
        if(isset($info["interact_button"])){
            $this->addComponent(new InteractButtonComponent($info["interact_button"]));
        }
        if(isset($info["knockback_resistance"])){
            $this->addComponent(new KnockbackResistanceComponent($info["knockback_resistance"]));
        }
        if(isset($info["max_stack_size"])){
            $this->maxStackSize = $info["max_stack_size"];
        }
    }

    public function getMaxStackSize() : int{
        return $this->maxStackSize;
    }

}