<?php
/**
 * Jefferson Porto
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  Az2009
 * @package   Az2009_Rede
 *
 * @copyright Copyright (c) 2018 Jefferson Porto - (https://www.linkedin.com/in/jeffersonbatistaporto/)
 *
 * @author    Jefferson Porto <jefferson.b.porto@gmail.com>
 */
namespace Az2009\Rede\Plugin\Widget;

class Context
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helper;

    /**
     * @var \Az2009\Rede\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Helper\Data $helper,
        \Az2009\Rede\Helper\Data $_helper
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->_helper = $_helper;
    }

    public function afterGetButtonList(
        \Magento\Backend\Block\Widget\Context $subject,
        $buttonList
    )
    {
        $this->addButtonPaymentUpdate($buttonList);
        return $buttonList;
    }

    public function addButtonPaymentUpdate(\Magento\Backend\Block\Widget\Button\ButtonList $buttonList)
    {
        if ($this->registry->registry('current_invoice')) {
            return $this;
        }

        $order = $this->registry->registry('current_order');

        if ($this->_helper->canAuthorizeOffline($order)) {
            $buttonList->add(
                'authorize_payment_offline',
                [
                    'label' => __('Capture Offline'),
                    'onclick' => "setLocation('{$this->helper->getUrl('rede/payment/authorize', ['order_id' => $order->getId()])}')"
                ]
            );
        }

        if ($this->_helper->canCancelOffline($order)) {
            $buttonList->add(
                'deny_payment_offline',
                [
                    'label' => __('Deny Offline'),
                    'onclick' => "setLocation('{$this->helper->getUrl('rede/payment/deny', ['order_id' => $order->getId()])}')"
                ]
            );
        }

        if ($this->_helper->canUpdate($order)) {
            $buttonList->add(
                'update_payment',
                [
                    'label' => __('Get Update Payment'),
                    'onclick' => "setLocation('{$this->helper->getUrl('rede/payment/update', ['order_id' => $order->getId()])}')"
                ]
            );
        }
    }
}