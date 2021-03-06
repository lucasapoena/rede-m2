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
namespace Az2009\Rede\Block\Form;

class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var \Az2009\Rede\Model\RedeConfigProvider
     */
    public $config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Az2009\Rede\Model\RedeConfigProvider $config,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->config = $config;
    }

    /**
     * Get the flags availables
     *
     * @return array|mixed
     */
    public function getCcAvailableTypes()
    {
        return $this->config->getCcAvailableTypes();
    }

    /**
     * Get flag image of card
     *
     * @param $code
     *
     * @return \stdClass
     */
    public function getIcon($code)
    {
        return $this->config->getIconByCode($code);
    }

    /**
     * Get code of method payment
     *
     * @return string
     */
    public function getCode()
    {
        return \Az2009\Rede\Model\Method\Cc\Cc::CODE_PAYMENT;
    }
}