<?php

namespace Magentoyan\SpinToWin\Controller\Ajaxoperation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magentoyan\SpinToWin\Model\SpinToWinSlices as SlicesModel;
use Magentoyan\SpinToWin\Model\SpinToWinCustomer as SpinToWinCustomerModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

use Magento\Backend\Model\Session as GlobalSession;

use Magento\Customer\Model\Session as CustomerSession;
use Magentoyan\SpinToWin\Helper\Data as MyHelper;

class DooStart extends Action {

    protected $_globalSession;
    protected $_model;
    protected $_spinCustomerModel;
    protected $customer_session;
    protected $_timezone;
    protected $rewardsBalance;
    protected $_customerRepository;
    protected $_helper;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            JsonFactory $resultJsonFactory,
            SlicesModel $myModel,
            CustomerSession $customer_session,
            GlobalSession $globalSession,
            TimezoneInterface $timezone,
            \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
            \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
            SpinToWinCustomerModel $spinCustomerModel,
            MyHelper $myHelper
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->_globalSession = $globalSession;
        $this->customer_session = $customer_session;
        $this->_helper = $myHelper;
        
        parent::__construct($context);

        
        $this->_model = $myModel;
        $this->_spinCustomerModel = $spinCustomerModel;
        $this->_timezone = $timezone;
        
        $this->_customerRepository = $customerRepository;        
        $this->rewardsBalance = $rewardsBalance;
    }

    public function execute() {

        $resultPage = $this->resultJsonFactory->create();

        try {
            

            $errorMsg = '';
            
            if (!$this->customer_session->isLoggedIn())
            {
                throw new \Exception(__('please loggin'));
            }
            
            $myParamsArr = [
                
                'number_of_customer_club_points_value' => ($this->getBalancePoints()),
                'number_of_customer_club_points_msg' => __("Number of customer club points %1", $this->getBalancePoints()),
                    ];
            
            

            $output = [
                'error_msg' => $errorMsg,
                'my_params_arr' => $myParamsArr,               
            ];
            
        } catch (\Exception $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => ['choosen_index' => -1],
            ];
        }catch (\ErrorException $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => ['choosen_index' => -1],
            ];
        }catch (\Error $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => ['choosen_index' => -1],
            ];
        }

        return $resultPage->setData($output);
    }
    
    private function getBalancePoints() {
        return $this->rewardsBalance->getBalancePoints($this->getCustomer());
    }
    
    private function countSpinnedBefore(){
        
        $customerId = $this->customer_session->getCustomerId(); 
        
        $count = $this->_spinCustomerModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', 'complete')
                ->count();
        
        return $count;
    }
    
    private function getCustomer() {
        
        $customerId = $this->customer_session->getCustomerId();        
        
        return $this->_customerRepository->getById($customerId);       
    }
}
