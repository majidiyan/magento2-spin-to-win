<?php

namespace Magentoyan\SpinToWin\Model\ResourceModel\SpinToWinCustomer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    
    protected function _construct()
    {
        $this->_init('Magentoyan\SpinToWin\Model\SpinToWinCustomer', 'Magentoyan\SpinToWin\Model\ResourceModel\SpinToWinCustomer');
    }

}
