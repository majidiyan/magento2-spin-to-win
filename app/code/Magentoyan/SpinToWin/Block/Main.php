<?php

namespace Magentoyan\SpinToWin\Block;

use Magento\Framework\View\Element\Template;
use Magentoyan\SpinToWin\Helper\Data as MyHelper;
use Magentoyan\SpinToWin\Model\SpinToWinSlices as SlicesModel;
use Magento\Customer\Model\Context as CustomerContext;
use Magentoyan\SpinToWin\Model\SpinToWinCustomer as SpinToWinCustomerModel;

class Main extends Template {

    protected $_helper;
    protected $_model;
    protected $httpContext;
    
    protected $_customerRepository;
    
    protected $rewardsBalance;
    protected $_spinCustomerModel;

    public function __construct(
            Template\Context $context,
            MyHelper $myHelper,
            SlicesModel $myModel,
            \Magento\Framework\App\Http\Context $httpContext,
            \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
            
            \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
            SpinToWinCustomerModel $spinCustomerModel,
            array $data = []
    ) {
        
        
        parent::__construct($context, $data);

        $this->_helper = $myHelper;
        $this->_model = $myModel;
        $this->httpContext = $httpContext;
        
        $this->_customerRepository = $customerRepository;        
        $this->rewardsBalance = $rewardsBalance;
        $this->_spinCustomerModel = $spinCustomerModel;
    }

    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }

    

    public function getPrizes() {

        $result = [];

        $collection = $this->_model->getCollection()
                ->addOrder('entity_id', 'ASC');

        foreach ($collection as $row) {
            $background_color = $row->getData('background_color');

            $result[] = [
                'text' => $row->getData('label'),
                'reaction' => $row->getData('reaction'),
                'text_color' => $row->getData('text_color'),
                'color' => call_user_func(function () use ($background_color) {

                    $background_colorHslArray = $this->_helper->rgbToHsl($background_color);

                    $background_colorHsl = "hsl({$background_colorHslArray[0]} {$background_colorHslArray[1]}% {$background_colorHslArray[2]}%)";

                    return $background_colorHsl;
                }),
            ];
        }

        $result = json_encode($result, JSON_PRETTY_PRINT, 512);
        $result = str_replace([
                    '"text":',
                    '"reaction":',
                    '"color":'
                ],
                [
                    'text:',
                    'reaction:',
                    'color:'
                ], $result);

        return $result .';';
    }
    
    
    public function isCustomerLoggedIn(){
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
