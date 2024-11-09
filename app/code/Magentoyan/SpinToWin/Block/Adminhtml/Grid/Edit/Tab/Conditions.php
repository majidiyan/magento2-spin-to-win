<?php

namespace Magentoyan\SpinToWin\Block\Adminhtml\Grid\Edit\Tab;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\SalesRule\Model\Rule;

use Magentoyan\SpinToWin\Model\RuleHelper;

/**
 * Block for rendering Conditions tab on Sales Rules creation page.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_apply_to';

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;
    
    protected $_dataCondition;
    
    private $_ruleHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param array $data
     * @param \Magento\SalesRule\Model\RuleFactory|null $ruleFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
            
        Rule $dataCondition,
        RuleHelper $ruleHelper,
        array $data = [],
        \Magento\SalesRule\Model\RuleFactory $ruleFactory = null
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->ruleFactory = $ruleFactory ?: ObjectManager::getInstance()
            ->get(\Magento\SalesRule\Model\RuleFactory::class);
        
        $this->_dataCondition = $dataCondition;
        $this->_ruleHelper = $ruleHelper;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of conditions tab to supplied form.
     *
     * @param Rule $model
     * @param string $fieldsetId
     * @param string $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'employee_form')
    {
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->ruleFactory->create();
            $model->load($id);
        }
        
        if(gettype($model) == 'object')
        {
            if(get_class() != 'Magento\SalesRule\Model\Rule')
            {
                $model = $this->ruleFactory->create();
                
                //Feedback saved serialized_condition to the Form
                $model2 = $this->_coreRegistry->registry('row_data');
                if($model2->getId() > 0){
                    
                    if(!empty($model2->getData('conditions_serialized')))
                    {
                        $arr = json_decode($model2->getData('conditions_serialized'), true, 512);
                        
                        $savedConditions = $this->_ruleHelper->getConditions()
                                ->setConditions([])
                                ->loadArray($arr);
                        
                        $model->setConditions($savedConditions);                                   
                    }                    
                }                
                //Feedback saved serialized_condition to the Form End
            }
        }
        
        $conditionsFieldSetId = 'conditions';//$model->getConditionsFieldSetId($formName);
        
        $newChildUrl = $this->getUrl(
            'sales_rule/promo_quote/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->getLayout()->createBlock(Fieldset::class);
        $renderer->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );
        $fieldset->addField(
            'conditions_serialized',
            'text',
            [
                'name'           => 'conditions_serialized',
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $form->setValues($model->getData());
        $this->setConditionFormName($this->_dataCondition->getConditions(), $formName);//???
        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {            
            foreach ($conditions->getConditions() as $condition) {               
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
