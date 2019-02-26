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
var config = {
    "map":{
      '*': {
          'Az2009_Cielo/js/cc/validate': 'Az2009_Cielo/js/cc/validate'
      }
    },
    shim: {
        'Az2009_Cielo/js/cc/validate': {
            deps: [
                'jquery',
                'jquery/validate',
            ]
        }
    }
};