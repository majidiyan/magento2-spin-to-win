<?php
namespace Magentoyan\SpinToWin\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    
    

    public function execute() {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        
        
        
        $params = $this->getRequest()->getParams();
        
        
        
        
        return $resultPage;
    }
}