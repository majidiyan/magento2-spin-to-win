<?php

namespace Magentoyan\SpinToWin\Model;

use Magentoyan\SpinToWin\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'magentoyan_spintowin';

    /**
     * @var string
     */
    protected $_cacheTag = 'magentoyan_spintowin';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magentoyan_spintowin';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Magentoyan\SpinToWin\Model\ResourceModel\Grid');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

   
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
    
    
    public function getReaction()
    {
        return $this->getData(self::REACTION);
    }

    public function setReaction($reaction)
    {
        return $this->setData(self::REACTION, $reaction);
    }

    
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }
    
    
    public function getBackgroundColor()
    {
        return $this->getData(self::BACKGROUND_COLOR);
    }

    public function setBackgroundColor($backgroundColor)
    {
        return $this->setData(self::BACKGROUND_COLOR, $backgroundColor);
    }
    
   
    public function getTextColor(){
        return $this->getData(self::TEXT_COLOR);
    }

    public function setTextColor($textColor){
        return $this->setData(self::TEXT_COLOR, $textColor);
    }
    
    public function getChance(){
        return $this->getData(self::CHANCE);
    }

    public function setChance($chance){
        return $this->setData(self::CHANCE, $chance);
    }

   
    public function getFreeShipping(){
        return $this->getData(self::FREE_SHIPPING);
    }

    public function setFreeShipping($freeShipping){
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
    }
    
    public function getCouponTypeAction(){
        return $this->getData(self::COUPON_TYPE_ACTION);
    }

    public function setCouponTypeAction($couponTypeAction){
        return $this->setData(self::COUPON_TYPE_ACTION, $couponTypeAction);
    }
    
    public function getCouponValueAction(){
        return $this->getData(self::COUPON_VALUE_ACTION);
    }
    

    public function setCouponValueAction($couponValueAction){
        return $this->setData(self::COUPON_VALUE_ACTION, $couponValueAction);
    }
    
    public function getCouponLifetime(){
        return $this->getData(self::COUPON_LIFETIME);
    }

    public function setCouponLifetime($couponLifetime){
        return $this->setData(self::COUPON_LIFETIME, $couponLifetime);
    }
    
    
    
    public function getPooch(){
        return $this->getData(self::POOCH);
    }

    public function setPooch($pooch){
        return $this->setData(self::POOCH, $pooch);
    }
    
    public function getConditionsSerialized(){
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    public function setConditionsSerialized($conditionsSerialized){
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }
    
    public function getDescription(){
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description){
        return $this->setData(self::DESCRIPTION, $description);
    }
    
    
}
