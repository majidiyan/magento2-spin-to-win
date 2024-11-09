<?php

namespace Magentoyan\SpinToWin\Model\ResourceModel;

class SpinToWinSlices extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('spin_to_win_slices', 'entity_id');
    }

}
