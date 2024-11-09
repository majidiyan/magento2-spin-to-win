<?php

namespace Magentoyan\SpinToWin\Api\Data;

interface GridInterface {

    
    const ID = 'entity_id';   
    
    const REACTION = 'reaction';
    
    const LABEL = 'label';  
    
    const BACKGROUND_COLOR = 'background_color'; 
    
    const TEXT_COLOR = 'text_color';  
    
    const CHANCE = 'chance'; 
    
    const FREE_SHIPPING = 'free_shipping'; 
    const COUPON_TYPE_ACTION = 'coupon_type_action'; 
    const COUPON_VALUE_ACTION = 'coupon_value_action'; 
    const COUPON_LIFETIME = 'coupon_lifetime'; 
    
    const POOCH = 'pooch';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const DESCRIPTION = 'description';
    
          
     
    public function getId();

    public function setId($id);
    
    public function getReaction();

    public function setReaction($reaction);

    
    public function getLabel();

    public function setLabel($label);
    

    public function getBackgroundColor();

    public function setBackgroundColor($backgroundColor);
    
    public function getTextColor();

    public function setTextColor($textColor);
    
    public function getChance();

    public function setChance($chance);
    
    
    public function getFreeShipping();

    public function setFreeShipping($freeShipping);
    
    public function getCouponTypeAction();

    public function setCouponTypeAction($couponTypeAction);
    
    public function getCouponValueAction();

    public function setCouponValueAction($couponValueAction);
    
    public function getCouponLifetime();

    public function setCouponLifetime($couponLifetime);
    
    
    
    public function getPooch();

    public function setPooch($pooch);
    
    public function getConditionsSerialized();

    public function setConditionsSerialized($conditionsSerialized);
    
    public function getDescription();

    public function setDescription($description);
   
}
