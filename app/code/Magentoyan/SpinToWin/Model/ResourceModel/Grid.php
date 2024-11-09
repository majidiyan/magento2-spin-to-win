<?php
/**
 * Grid Grid ResourceModel.
 * @category  Magentoyan
 * @package   Magentoyan_SpinToWin
 * @author    Magentoyan
 * @copyright Copyright (c) 2010-2016 Magentoyan Software Private Limited (https://magentoyan.com)
 * @license   https://store.magentoyan.com/license.html
 */
namespace Magentoyan\SpinToWin\Model\ResourceModel;

/**
 * Grid Grid mysql resource.
 */
class Grid extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('spin_to_win_slices', 'entity_id');
    }
}
