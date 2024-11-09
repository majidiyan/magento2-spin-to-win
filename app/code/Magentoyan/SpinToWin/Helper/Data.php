<?php

namespace Magentoyan\SpinToWin\Helper;

use Magento\SalesRule\Model\Rule as SalesRuleModel;
use Magento\SalesRule\Model\RuleFactory as SalesRuleFactoryModel;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public $H;
    public $S;
    public $L;
    public $R;
    public $G;
    public $B;
    protected $_salesRuleModel;
    protected $_salesRuleFactory;
    protected $_scopeConfig;

    public function __construct(
            /* codes here */
            SalesRuleModel $salesRuleModel,
            SalesRuleFactoryModel $salesRuleFactory,
            /* codes here end */
            \Magento\Framework\App\Helper\Context $context
    ) {

        /* codes here */
        $this->_salesRuleModel = $salesRuleModel;
        $this->_salesRuleFactory = $salesRuleFactory;
        /* codes here end */
        parent::__construct($context);
        
        $this->_scopeConfig = $context->getScopeConfig();
    }
    
    public function getConfig($value) {


        return $this->_scopeConfig->getValue('magentoyan_spintowin/general/' . $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function rgbToHsl($rgb = '#000000') {

        $rgb = strtolower($rgb);

        list($r, $g, $b) = sscanf($rgb, "#%02x%02x%02x");

        $this->R = $r;
        $this->G = $g;
        $this->B = $b;

        $r = ($this->R / 255.0);
        $g = ($this->G / 255.0);
        $b = ($this->B / 255.0);

        $min = min(min($r, $g), $b);
        $max = max(max($r, $g), $b);
        $delta = $max - $min;

        $this->L = ($max + $min) / 2;

        if ($delta == 0) {
            $this->H = 0;
            $this->S = 0.0;
        } else {
            $this->S = ($this->L <= 0.5) ? ($delta / ($max + $min)) : ($delta / (2 - $max - $min));

            if ($r == $max) {
                $hue = (($g - $b) / 6) / $delta;
            } else if ($g == $max) {
                $hue = (1.0 / 3) + (($b - $r) / 6) / $delta;
            } else {
                $hue = (2.0 / 3) + (($r - $g) / 6) / $delta;
            }

            if ($hue < 0)
                $hue += 1;
            if ($hue > 1)
                $hue -= 1;

            $this->H = (int) ($hue * 360);
        }

        return [$this->H, $this->S * 100, $this->L * 100];
    }

    public function generateCoupon($params) {

        try {
            do {
                $couponCode = $this->generateCouponCode();

                $count = $this->_salesRuleModel->getCollection()
                                ->addFieldToFilter('code', $couponCode)->count();
            } while ($count != 0);
            
            /*
             'to_percent'

             'by_percent'

             'to_fixed'

             'by_fixed'

             'cart_fixed'

             'buy_x_get_y'
            */
    
            switch($params['coupon_type_action'])
            {
                case 'percent':
                    $simple_action = 'by_percent';
                    break;
                
                default :
                    $simple_action = 'cart_fixed';
            }

            $rule = $this->_salesRuleFactory->create();
            $rule->setName("Spin To Win $couponCode")
                    ->setDescription(__('Spin To Win'))
                    ->setFromDate($params['date_from'])
                    ->setToDate($params['date_to'])
                    ->setCouponType(2) // Specific coupon
                    ->setCouponCode($couponCode)
                    ->setUsesPerCoupon(1)
                    ->setUsesPerCustomer(1)
                    ->setIsActive(1)
                    ->setConditionsSerialized($params['conditions_serialized'])
                    ->setActionsSerialized(null)
                    ->setCustomerGroupIds([0, 1, 2, 3]) // All customer groups
                    ->setDiscountAmount($params['coupon_value_action'])
                    ->setData('simple_action', $simple_action)
                    ->setWebsiteIds([1]) // Website ID
                    ->save();
            
            if(!($rule->getId() > 0))
                throw new \Exception(__('create coupon failed'));
            
            return [
                'coupon_code' => $couponCode, 
                'status' => true,
                'description' => $params['description'],
                ];
            
        } catch (\Exception $ex) {
            return ['coupon_code' => $ex->getMessage(), 'status' => false];
        } catch (\ErrorException $ex) {
            return ['coupon_code' => $ex->getMessage(), 'status' => false];
        } catch (\Error $ex) {
            return ['coupon_code' => $ex->getMessage(), 'status' => false];
        }
    }

    private function generateCouponCode() {
        $numAndAlphabet = [
            ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        ];

        $result = '';

        for ($i = 0; $i < 8; $i++) {
            $k = ($i < 3 ? 0 : 1);
            $length = count($numAndAlphabet[$k]);
            $k2 = rand(0, $length - 1);
            $char = $numAndAlphabet[$k][$k2];
            $result .= $char;
        }
        return $result;
    }
}
