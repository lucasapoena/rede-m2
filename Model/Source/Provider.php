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
namespace Az2009\Cielo\Model\Source;

class Provider
{
    /**
     * @return string[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Bradesco (Not Registered)'), 'value' => 'Bradesco'],
            ['label' => __('Banco do Brasil (Not Registered)'), 'value' => 'BancoDoBrasil'],
            ['label' => __('Bradesco (Registered)'), 'value' => 'Bradesco2'],
            ['label' => __('Banco do Brasil (Registered)'), 'value' => 'BancoDoBrasil2']
        ];
    }
}