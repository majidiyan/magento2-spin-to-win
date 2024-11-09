<?php

namespace Magentoyan\SpinToWin\Controller\Adminhtml\Grid;

use Magentoyan\SpinToWin\Model\RuleHelper;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magentoyan\SpinToWin\Model\GridFactory
     */
    var $gridFactory;
    
    protected $_ruleHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magentoyan\SpinToWin\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magentoyan\SpinToWin\Model\GridFactory $gridFactory,
         RuleHelper $ruleHelper
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->_ruleHelper = $ruleHelper;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('spintowin/grid/addrow');
            return;
        }
        try {
            
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
                $arr = $this->_ruleHelper->loadMyPost($data);
                $data['conditions_serialized'] = json_encode($arr['conditions'][1]);
            }


            $rowData = $this->gridFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('spintowin/grid/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magentoyan_SpinToWin::save');
    }
}
