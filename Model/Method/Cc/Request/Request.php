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
namespace Az2009\Rede\Model\Method\Cc\Request;

use Magento\Framework\Event\ManagerInterface;

class Request extends \Az2009\Rede\Model\Method\Request
{

    protected $_prefixDispatch = 'after_prepare_request_params_rede_cc';

    public function __construct(
        Customer $customer,
        Payment $payment,
        ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($customer, $payment, $eventManager, $data);
    }

}