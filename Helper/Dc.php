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
namespace Az2009\Rede\Helper;

class Dc extends Data
{
    const URL_REDIRECT = 'rede/process/payment/';

    /**
     * Get return url to redirect after authentication in provider
     *
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->_getUrl(self::URL_REDIRECT);
    }

    /**
     * @return array|mixed
     */
    public function getCardTypesAvailable()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_rede_dc/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $config = explode(',', $config);

        return $config;
    }
}