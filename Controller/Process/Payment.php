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
namespace Az2009\Rede\Controller\Process;

class Payment extends \Az2009\Rede\Controller\Postback\Index
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Checkout\Model\Type\OnepageFactory
     */
    protected $_onepageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Az2009\Rede\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Magento\Checkout\Model\Type\OnepageFactory $onepageFactory
    ) {
        $this->_order = $order;
        $this->_onepageFactory = $onepageFactory;
        parent::__construct($context,$session,$logger,$helper,$registry);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $url = 'checkout/onepage/failure/';
        $session = $this->getOnepage()->getCheckout();
        $orderId = $session->getLastOrderId();
        $request = $this->getRequest();
        $order = $this->_order->load($orderId);

        try {

            if (!$this->_isValid()) {
                throw new \Exception(__('Request Invalid'));
            }

            if (!$order->getId()) {
                $url = '/';
                throw new \Exception(__('Order Not Found'));
            }

            if (!$this->_isAuthorize()) {
                throw new \Az2009\Rede\Exception\Cc(__($request->getParam('returnMessage')));
            }

            $this->processTransaction($order);

            if ($this->registry->registry('payment_captured')) {
                return $this->_redirect('checkout/onepage/success/');
            }

        } catch(\Az2009\Rede\Exception\Cc $e) {
            if ($order->getId()) {
                $order->registerCancellation($e->getMessage())->save();
            }

            $this->messageManager->addError($e->getMessage());
        } catch(\Exception $e) {
            if ($order->getId()) {
                $order->registerCancellation($e->getMessage())->save();
            }
            $this->logger->info($e->getMessage());
            $this->messageManager->addError(__('Occurred an error during payment process. Contact the store.'));
        }

        return $this->_redirect($url);
    }

    /**
     * Get one page checkout model
     *
     * @return \Magento\Checkout\Model\Type\Onepage
     * @codeCoverageIgnore
     */
    public function getOnepage()
    {
        return $this->_onepageFactory->create();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws \Exception
     */
    public function processTransaction(\Magento\Sales\Model\Order $order)
    {
        $payment = $order->getPayment();
        $postback = $payment->getMethodInstance()
                            ->getPostbackInstance();

        if ($postback !== null) {
            $postback = $this->_objectManager->get($postback);
        }

        if (!($postback instanceof \Az2009\Rede\Model\Method\AbstractMethod)) {
            throw new \Exception((string)__('Order Not Found to PaymentId %1', $this->_paymentId));
        }

        $postback->setPaymentId($this->_paymentId)
                 ->setIsBackground(true)
                 ->process();
    }

    /**
     * Check if the transaction was authorized
     *
     * @return bool
     */
    protected function _isAuthorize()
    {
        $request = $this->getRequest();
        $tid = $request->getParam('tid');
        $reference = $request->getParam('reference');
        if (empty($tid) && !empty($reference)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function _isValid()
    {
        $request = $this->getRequest();
        if(!$request->isPost()) {
            return false;
        }

        if(!($this->_paymentId = $request->getParam('tid'))
            && !$request->getParam('reference')
        ) {
            return false;
        }

        return true;
    }

}