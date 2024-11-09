<?php

namespace Magentoyan\SpinToWin\Model\ResourceModel;

class SpinToWinCustomer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('spin_to_win_customer', 'entity_id');
    }

}
