<?php

namespace Magentoyan\SpinToWin\Block\Adminhtml\Grid\Edit;


class GenericButton {
     /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->context      = $context;
        $this->registry     = $registry;
        $this->urlBuilder   = $context->getUrlBuilder();
    }

    public function getId()
    {
        $model = $this->registry->registry('row_data');
        return $model ? $model->getId() : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
