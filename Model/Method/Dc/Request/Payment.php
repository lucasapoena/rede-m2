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
namespace Az2009\Rede\Model\Method\Dc\Request;

class Payment extends \Az2009\Rede\Model\Method\Cc\Request\Payment
{

    const TYPE = 'debit';

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    public function __construct(
        \Az2009\Rede\Model\Source\Cctype $cctype,
        \Az2009\Rede\Helper\Dc $helper,
        \Magento\Framework\HTTP\Header $httpHeader,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->httpHeader = $httpHeader;
        parent::__construct($cctype, $helper, $data);
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        $this->order = $this->getOrder();
        $payment = $this->order
                        ->getPayment()
                        ->getMethodInstance();

        $info = $payment->getInfoInstance();

        $this->setInfo($info);
        $this->setPayment($payment);

        return $this->setData(
            [
                'kind' => Payment::TYPE,
                'Amount' => $this->helper->formatNumber($info->getAmount()),
                'capture' => true,
                'softDescriptor' => $this->helper->prepareString($this->getSoftDescriptor(), 13, 0),
                'cardNumber' => $this->getInfo()->getAdditionalInformation('cc_number'),
                'cardHolderName' => $this->getInfo()->getAdditionalInformation('cc_name'),
                'expirationMonth' => $this->getExpMonth(),
                'expirationYear' => $this->getExpYear(),
                'subscription' => false,
                'Origin' => 1,
                'distributorAffiliation' => $this->helper->getMerchantId(),
                'securityCode' => $this->getInfo()->getAdditionalInformation('cc_cid'),
                'Brand' => $this->_cctype->getBrandFormatRede($this->getInfo()->getAdditionalInformation('cc_type')),
                'threeDSecure' => [
                    'embedded' => true,
                    'onFailure' => 'decline',
                    'userAgent' => $this->httpHeader->getHttpUserAgent()
                ],
                'urls' =>
                    [
                        [
                            'kind' => 'threeDSecureSuccess',
                            'url'  => $this->getReturnUrl()
                        ],
                        [
                            'kind' => 'threeDSecureFailure',
                            'url'  => $this->getReturnUrl()
                        ]
                    ]
            ]
        )->toArray();
    }

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->helper->getReturnUrl();
    }

}