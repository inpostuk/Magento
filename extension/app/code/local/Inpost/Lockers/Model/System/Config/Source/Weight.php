<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_System_Config_Source_Weight
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'kg', 'label'=>Mage::helper('adminhtml')->__('kg')),
            array('value' => 'lb', 'label'=>Mage::helper('adminhtml')->__('lb')),
            array('value' => 'g', 'label'=>Mage::helper('adminhtml')->__('g')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'g' => Mage::helper('adminhtml')->__('g'),
            'lb' => Mage::helper('adminhtml')->__('lb'),
            'kg' => Mage::helper('adminhtml')->__('kg'),
        );
    }

}
