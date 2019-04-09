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
namespace Az2009\Rede\Controller\Postback;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string
     */
    protected $_paymentId;

    /**
     * @var string
     */
    protected $_changeType;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Az2009\Rede\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Az2009\Rede\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->_session = $session;
        $this->logger = $logger;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(403);
        $msg = '';
        if ($this->_isValid()) {
            try {

                $postback = $this->helper->getPostbackByTransId($this->_paymentId);
                if (!($postback instanceof \Az2009\Rede\Model\Method\AbstractMethod)) {
                    throw new \Exception((string)__('Order Not Found to PaymentId %1', $this->_paymentId));
                }

                $postback->setPaymentId($this->_paymentId)
                         ->setIsBackground(true)
                         ->process();

                $response->setHttpResponseCode(200);

            } catch(\Exception $e) {
                $code = mt_rand(2, 9999);
                $msg = __('CodeError: %1', $code);
                $this->logger->error(__("\n \n \n PostbackError: \n Code: %1 \n Message: %2", $code, $e->getMessage()));
                $response->setHttpResponseCode(500);
            }

            $response->clearBody();
            $response->setBody($msg);
            $response->sendHeaders();
        }
    }

    /**
     * @return bool
     */
    protected function _isValid()
    {
        $request = $this->getRequest();

         if(!$request->isPost()
            || !($this->_paymentId = $request->getParam('tid'))

        ) {
            return false;
        }

        return true;
    }

}