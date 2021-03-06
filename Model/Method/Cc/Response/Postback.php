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
namespace Az2009\Rede\Model\Method\Cc\Response;

class Postback extends \Az2009\Rede\Model\Method\Cc\Response\Payment
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    public function __construct(
        \Az2009\Rede\Model\Method\Cc\Transaction\Authorize $authorize,
        \Az2009\Rede\Model\Method\Cc\Transaction\Unauthorized $unauthorized,
        \Az2009\Rede\Model\Method\Cc\Transaction\Capture $capture,
        \Az2009\Rede\Model\Method\Cc\Transaction\Pending $pending,
        \Az2009\Rede\Model\Method\Cc\Transaction\Cancel $cancel,
        \Magento\Sales\Model\Order $order,
        array $data = []
    ) {
        $this->_order = $order;

        parent::__construct($authorize, $unauthorized, $capture, $pending, $cancel, $data);
    }

    public function process()
    {
        if (!$this->getPayment()) {
            $this->_getPaymentInstance();
        }

        switch ($this->getStatus()) {
            case Payment::STATUS_CAPTURED:
                $this->_capture
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->setPostback($this->getIsBackground())
                     ->process();
            break;
            case Payment::STATUS_CANCELED_DENIED:
            case Payment::STATUS_CANCELED:
                $this->_cancel
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->setPostback($this->getIsBackground())
                    ->process();
            break;
            case Payment::STATUS_PENDING:
                $this->_pending
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->process();
            break;
            default:
                $this->_unauthorized
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->process();
            break;
        }
    }

    /**
     * get payment instance of order
     * @return $this
     */
    protected function _getPaymentInstance()
    {
        $orderId = $this->_getMerchantOrderId();
        $this->_order = $this->_order->loadByIncrementId($orderId);
        $this->setOrder($this->_order);
        $this->setPayment($this->_order->getPayment());

        return $this;
    }

    /**
     * get order id of post
     * @return mixed
     * @throws \Exception
     */
    protected function _getMerchantOrderId()
    {
        $body = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($body['authorization']['reference'])) {
            throw new \Exception(__('Proper reference not found'));
        }

        return $body['authorization']['reference'];
    }

    /**
     * get status payment
     * @return mixed
     * @throws \Exception
     */
    public function getStatus()
    {
        $body = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($body['authorization']['status'])) {
            throw new \Exception(__('Status transaction not found'));
        }

        return $body['authorization']['status'];
    }
}