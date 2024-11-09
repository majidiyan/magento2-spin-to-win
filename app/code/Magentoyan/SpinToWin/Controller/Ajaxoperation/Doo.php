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

class Doo extends Action {

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

            if (!$this->customer_session->isLoggedIn()) {
                throw new \Exception(__('please loggin'));
            }

            if (!($this->getBalancePoints() > 0 || $this->countSpinnedBefore() == 0)) {
                throw new \Exception(__('Has no points'));
            }

            if (!( intval($this->_helper->getConfig('use_reward_points')) > 0 )) {
                throw new \Exception(__('use_reward_points empty'));
            }

            if ($this->countSpinnedBefore() > 0 &&
                    intval($this->_helper->getConfig('use_reward_points')) > $this->getBalancePoints()
            ) {
                throw new \Exception(__('your points is not enough'));
            }

            $beforeUsed = $this->countSpinnedBefore(); /* num before spinned */


            $collection = $this->_model->getCollection()
                    ->addOrder('entity_id', 'ASC');

            $ids = [];
            $weights = [];
            $results = [];
            $resultRand = null;
            $b = true;
            $index = -1;

            foreach ($collection as $row) {
                $index++;
                $results[] = $index;
                $weights[] = $row->getData('chance') / 100;
                $ids[] = $row->getId();
            }



            $totalCount = 0;
            $countNumberFour = 0;
            $num = mt_rand() / mt_getrandmax();
            $s = 0;
            $lastIndex = count($weights) - 1;

            for ($i = 0; $i < $lastIndex; ++$i) {
                $s += $weights[$i];
                if ($num < $s) {
                    $resultRand = $results[$i];
                    $b = false;
                    break;
                }
            }

            if ($b)
                $resultRand = $results[$lastIndex];

            $myParamsArr = [
                /*'choosen_index' => $resultRand,*/
                'before_used' => $beforeUsed,
                'confirm_msg' => __("%1 points will be deducted for re-use.",
                        [intval($this->_helper->getConfig('use_reward_points'))]),
            ];

            $choosenRecordId = $ids[$resultRand];

            $this->_globalSession->setChoosenIndex($resultRand);

            $output = [
                'error_msg' => $errorMsg,
                'my_params_arr' => $myParamsArr,
            ];

            $customerId = $this->customer_session->getCustomerId();

            //close all pendings for this customer
            $collection2 = $this->_spinCustomerModel->getCollection()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('status', 'pending');

            foreach ($collection2 as $item2) {
                $pk2 = $item2->getId();

                $item2->setData('status', 'closed')
                        ->setId($pk2)
                        ->save();
            }
            //close all pendings for this customer End
            //insert Record
            $todayDate = $this->_timezone->date()->format('Y-m-d H:i:s');
            $nowObject = new \DateTime($todayDate);
            $now = $nowObject->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

            $choosenRecord = $collection->getItemById($choosenRecordId);

            $this->_spinCustomerModel->setData([
                'customer_id' => $customerId,
                'status' => 'pending',
                'reaction' => $choosenRecord->getData('reaction'),
                'free_shipping' => $choosenRecord->getData('free_shipping'),
                'created_at' => $now
            ])->save();
            //insert Record End
        } catch (\Exception $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [/*'choosen_index' => -1*/]
            ];
        } catch (\ErrorException $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [/*'choosen_index' => -1*/]
            ];
        } catch (\Error $e) {
            $output = [
                'error_msg' => $e->getMessage(),
                'my_params_arr' => [/*'choosen_index' => -1*/]
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

        return $count;
    }

    private function getCustomer() {

        $customerId = $this->customer_session->getCustomerId();

        return $this->_customerRepository->getById($customerId);
    }
}
