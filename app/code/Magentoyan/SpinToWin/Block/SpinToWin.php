<?php

namespace Magentoyan\SpinToWin\Block;

use Magento\Customer\Model\Context as CustomerContext;
use Magentoyan\SpinToWin\Model\SpinToWinCustomer as SpinToWinCustomerModel;

class SpinToWin extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface {

    protected $_template = 'widget/spintowin.phtml';
    protected $httpContext;
    
    protected $_customerRepository;
    
    protected $rewardsBalance;
    protected $_spinCustomerModel;

    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\Http\Context $httpContext,
            \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
            
            \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
            SpinToWinCustomerModel $spinCustomerModel,
            array $data = []
    ) {

        

        parent::__construct($context, $data);
        
        $this->httpContext = $httpContext;
        
        $this->_customerRepository = $customerRepository;
        
        $this->rewardsBalance = $rewardsBalance;
        $this->_spinCustomerModel = $spinCustomerModel;
    }

   

    public function isCustomerLoggedIn() {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }
    
    public function getBalancePoints() {
        return $this->rewardsBalance->getBalancePoints($this->getCustomer());
    }
    
    public function countSpinnedBefore(){
        $customerId = $this->getCustomer()->getId();
        
        $count = $this->_spinCustomerModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->count();
        
        return $count;
    }
    
    protected function getCustomer() {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class); 
        $customer = $customerSession->getCustomer();
        
        
        return $this->_customerRepository->getById($customer->getId());       
    }
}
