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
define([], function () {
    'use strict';

    return function (value) {
        var month, len;

        if (value.match('/')) {
            value = value.split(/\s*\/\s*/g);

            return {
                month: value[0],
                year: value.slice(1).join()
            };
        }

        len = value[0] === '0' || value.length > 5 || value.length === 4 || value.length === 3 ? 2 : 1;
        month = value.substr(0, len);

        return {
            month: month,
            year: value.substr(month.length, 4)
        };
    };
});
