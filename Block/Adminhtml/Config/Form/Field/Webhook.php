<?php

namespace GhoSter\KbankPayments\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;

class Webhook extends Field
{
    /**
     * URL for backend webhook.
     */
    public const URI = 'rest/V1/kbank/payment/notify';

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;
    /**
     * @var Http
     */
    private $request;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Http $request,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * Get Element Html
     *
     * @param AbstractElement $element
     * @return string
     * @throws NoSuchEntityException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $storeId = $this->request->getParam('website') ?? $this->request->getParam('store');

        if ($storeId) {
            $this->storeManager->setCurrentStore($storeId);
        }

        return $this->storeManager->getStore()->getBaseUrl() . self::URI;
    }
}
