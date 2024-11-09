<?php

namespace Magentoyan\SpinToWin\Model;

/**
 * Description of RuleHelper
 *
 * @author magento
 */
class RuleHelper extends \Magento\Rule\Model\AbstractModel {

    protected $_couponFactory;
    protected $_codegenFactory;
    protected $_condCombineFactory;
    protected $_condProdCombineF;
    protected $_couponCollection;
    protected $_storeManager;

    public function __construct(
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
            /*majidian*/
            \Magento\SalesRule\Model\CouponFactory $couponFactory,
            \Magento\SalesRule\Model\Coupon\CodegeneratorFactory $codegenFactory,
            \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
            \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
            \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            /*majidian end*/
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
            array $data = [],
            \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory = null,
            \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory = null,
            \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {

        $this->_couponFactory = $couponFactory;
        $this->_codegenFactory = $codegenFactory;
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        $this->_couponCollection = $couponCollection;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data, $extensionFactory, $customAttributeFactory, $serializer);
    }

    public function getConditionsInstance() {
        return $this->_condCombineFactory->create();
    }

    public function getActionsInstance() {
        return $this->_condProdCombineF->create();
    }
    
    
    public function loadMyPost(array $data){
        $arr = $this->_convertFlatToRecursive($data);
        
        return $arr;
    }
}
