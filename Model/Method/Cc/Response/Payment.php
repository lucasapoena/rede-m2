<?php
/**
 * Jefferson Porto
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  Az2009
 * @package   Az2009_Cielo
 *
 * @copyright Copyright (c) 2018 Jefferson Porto - (https://www.linkedin.com/in/jeffersonbatistaporto/)
 *
 * @author    Jefferson Porto <jefferson.b.porto@gmail.com>
 */
namespace Az2009\Cielo\Model\Method\Cc\Response;

class Payment extends \Az2009\Cielo\Model\Method\Response
{

    const STATUS_CANCELED = 'Canceled';

    const STATUS_CAPTURED = 'Approved';

    const STATUS_PENDING = 'Pending';

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Authorize
     */
    protected $_authorize;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Capture
     */
    protected $_capture;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\General
     */
    protected $_pending;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Unauthorized
     */
    protected $_unauthorized;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel
     */
    protected $_cancel;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Postback
     */
    protected $_postback;

    public function __construct(
        \Az2009\Cielo\Model\Method\Cc\Transaction\Authorize $authorize,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Unauthorized $unauthorized,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Capture $capture,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Pending $pending,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel $cancel,
        array $data = []
    ) {
        $this->_unauthorized = $unauthorized;
        $this->_authorize = $authorize;
        $this->_capture = $capture;
        $this->_pending = $pending;
        $this->_cancel = $cancel;

        parent::__construct($data);
    }

    public function process()
    {
        parent::process();
        $body = $this->getBody();
        if (!property_exists($body, 'tid')) {
            throw new \Exception(__('Invalid payment Transaction ID'));
        }

        $objManager = \Magento\Framework\App\ObjectManager::getInstance();
        $postback = $objManager->get(\Az2009\Cielo\Model\Method\Cc\Postback::class);
        $postback->setPaymentId($body->tid);
        $postback->setIsBackground(false);
        $postback->setPaymentUpdate($this->getPayment());
        $postback->process();
    }
}