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
define(
    [
        'jquery',
        'mage/url'
    ],
    function ($, urlBuilder) {
        'use strict';
        return function () {
            var url = urlBuilder.build('rede/authenticate/index');
            $.mage.redirect(url);
        };
    }
);