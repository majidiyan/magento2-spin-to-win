<?php

namespace Magentoyan\SpinToWin\Block\Adminhtml\Grid\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

use Magentoyan\SpinToWin\Block\Adminhtml\Grid\Edit\GenericButton;

/**
 * Description of SaveButton
 *
 * @author ali
 */
class SaveButton extends GenericButton implements ButtonProviderInterface {
    
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
