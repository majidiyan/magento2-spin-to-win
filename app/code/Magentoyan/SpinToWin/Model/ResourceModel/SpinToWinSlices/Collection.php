<?php

namespace Magentoyan\SpinToWin\Model\ResourceModel\SpinToWinSlices;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    
    protected function _construct()
    {
        $this->_init('Magentoyan\SpinToWin\Model\SpinToWinSlices', 'Magentoyan\SpinToWin\Model\ResourceModel\SpinToWinSlices');
    }

}
