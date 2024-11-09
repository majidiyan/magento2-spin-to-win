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

class Dox extends Action {

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

        parent::__construct($context);

        $this->_model = $myModel;
        $this->_spinCustomerModel = $spinCustomerModel;
        $this->_timezone = $timezone;

        $this->_customerRepository = $customerRepository;
        $this->rewardsBalance = $rewardsBalance;

        $this->_helper = $myHelper;
    }

    public function execute() {

        $resultPage = $this->resultJsonFactory->create();

        try {

            $params = $this->getRequest()->getParams();

            $errorMsg = '';
            $myParamsArr = [];
            
            $balancePointsBefore = $this->_globalSession->getBalancePointsBefore();

            if ($params['input_data'] != $this->_globalSession->getChoosenIndex()) {
                throw new \Exception(__('invalid index'));
            }

            $this->_globalSession->unsChoosenIndex();

            if (!$this->customer_session->isLoggedIn()) {
                throw new \Exception(__('please loggin'));
            }
            
            
            $this->updateHistory($myParamsArr);

            if (!($balancePointsBefore > 0 || $this->countSpinnedBefore() == 0)) {
                throw new \Exception(__('Has no points'));
            }

            if (!( intval($this->_helper->getConfig('use_reward_points')) > 0 )) {
                throw new \Exception(__('use_reward_points empty'));
            }

            if ($this->countSpinnedBefore() > 0 &&
                    intval($this->_helper->getConfig('use_reward_points')) > $balancePointsBefore
            ) {
                throw new \Exception(__('your points is not enough'));
            }
            
            

            //create coupon
            $customerId = $this->customer_session->getCustomerId();
            $activeRow = $this->_spinCustomerModel->getCollection()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('status', 'pending')
                    ->getLastItem();

            $activeReaction = $activeRow->getData('reaction');

            $activeSlice = $this->_model->load($activeReaction, 'reaction');

            $isPooch = $activeSlice->getData('pooch');

            if (!$isPooch) {

                $couponLifetime = $activeSlice->getData('coupon_lifetime');
                $couponTypeAction = $activeSlice->getData('coupon_type_action');
                $couponValueAction = $activeSlice->getData('coupon_value_action');
                $conditionsSerialized = $activeSlice->getData('conditions_serialized');
                $description = $activeSlice->getData('description');

                $todayDate = $this->_timezone->date()->format('Y-m-d');
                $nowObject = new \DateTime($todayDate);
                $now = $nowObject->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);

                $nowObject->modify("+$couponLifetime day");

                $toDate = $nowObject->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);

                $couponData = [
                    'date_from' => $now,
                    'date_to' => $toDate,
                    'coupon_type_action' => $couponTypeAction,
                    'coupon_value_action' => $couponValueAction,
                    'conditions_serialized' => $conditionsSerialized,
                    'description' => $description,
                ];
                $resultCoupon = $this->_helper->generateCoupon($couponData);

                if (!$resultCoupon['status']) {
                    throw new \Exception(__('create coupon failed'));
                }

               
                
                $myParamsArr['coupon_code'] = $resultCoupon['coupon_code'];
                $myParamsArr['description'] = $description;
            }
            //create coupon end
            
            //update table slice_customer            
            $pk = $activeRow->getId();

            $todayDate = $this->_timezone->date()->format('Y-m-d H:i:s');
            $nowObject = new \DateTime($todayDate);
            $now = $nowObject->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

            $activeRow->setData('status', 'complete')
                    ->setData('coupon_code', isset($resultCoupon['coupon_code']) ? $resultCoupon['coupon_code'] : '')
                    ->setData('updated_at', $now)
                    ->setId($pk)
                    ->save();
            //update table slice_customer end
            
            if(!isset($resultCoupon['coupon_code']))
                $errorMsg = 'pooch';

            $this->updateHistory($myParamsArr);
            
            $output = [
                'error_msg' => $errorMsg,
                'my_params_arr' => $myParamsArr,
            ];
        } catch (\Exception $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [],
            ];
        } catch (\ErrorException $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [],
            ];
        } catch (\Error $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [],
            ];
        }

        return $resultPage->setData($output);
    }

    private function getBalancePoints() {
        return $this->rewardsBalance->getBalancePoints($this->getCustomer());
    }

    private function countSpinnedBefore() {

        $customerId = $this->customer_session->getCustomerId();

        $count = $this->_spinCustomerModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->count();

        if ($count == 1) {
            $row = $this->_spinCustomerModel->getCollection()->getLastItem();
            if ($row->getData('status') == 'pending')
                $count = 0;
        }

        return $count;
    }
    
    private function countCompleteSpinnedBefore() {

        $customerId = $this->customer_session->getCustomerId();

        $count = $this->_spinCustomerModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', 'complete')
                ->count();
        
        return $count;
    }
    
    private function updateHistory(&$myParamsArr) : void {
        /* Number of times used */
            
            $aux = $this->getBalancePoints() == 0 ? '1' : ( intval($this->getBalancePoints() / $this->_helper->getConfig('use_reward_points') ) );
            
            
            $myParamsArr['number_of_customer_club_points_value'] = $this->getBalancePoints();
            $myParamsArr['number_of_customer_club_points_msg'] = __("Number of customer club points %1", $this->getBalancePoints());
            /* Number of times used End */
    }

    private function getCustomer() {

        $customerId = $this->customer_session->getCustomerId();

        return $this->_customerRepository->getById($customerId);
    }
}
