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
namespace Az2009\Rede\Model\Method\Cc\Transaction;

class Capture extends \Az2009\Rede\Model\Method\Transaction
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Az2009\Rede\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $session,
        \Az2009\Rede\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->_registry = $registry;
        parent::__construct($session, $comment, $data);
    }

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = null;

        if (!property_exists($this->getBody(), 'authorization') && !$payment->getLastTransId()) {
            throw new \Az2009\Rede\Exception\Cc(__('Payment not authorized'));
        } elseif(property_exists($this->getBody(), 'authorization')) {
            $paymentId = $this->getBody()->authorization->tid;
        }

        if (empty($paymentId) && !$payment->getLastTransId()) {
            throw new \Az2009\Rede\Exception\Cc(__('Payment not authorized'));
        }

        //check if is the first capture of order
        if (!$payment->getLastTransId() && !empty($paymentId)) {
            $payment->setTransactionId($paymentId)
                    ->setLastTransId($paymentId);
            $payment->setAdditionalInformation(
                'transaction_authorization',
                $paymentId
            );
        } else {
            $payment->setParentTransactionId(
                $payment->getAdditionalInformation('transaction_authorization')
            );
        }

        $this->prepareBodyTransaction($bodyArray);

        $payment->setTransactionAdditionalInfo(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $this->getTransactionData()
        );

        $payment->setIsTransactionClosed(true);

        $this->addReturnMessageToTransaction($bodyArray);
        if ($this->getPostback()) {
            $payment->registerCaptureNotification($this->_getCapturedAmount(), true);
            $payment->getOrder()->save();
        }

        $this->_registry->register('payment_captured', true);

        return $this;
    }

    protected function _getCapturedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['capture']['amount'])
            || !($captureAmount = doubleval($bodyArray['capture']['amount']))
        ) {
            throw new \Exception(
                __(
                    'Not exists values to capture in order %1',
                    $this->getPayment()->getOrder()->getId()
                )
            );
        }

        return $this->helper->convertToPrice($captureAmount);
    }
}