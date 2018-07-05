<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_System_Config_Source_Size
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'a', 'label'=>Mage::helper('adminhtml')->__('A')),
            array('value' => 'b', 'label'=>Mage::helper('adminhtml')->__('B')),
            array('value' => 'c', 'label'=>Mage::helper('adminhtml')->__('C')),
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
            'a' => Mage::helper('adminhtml')->__('A'),
            'b' => Mage::helper('adminhtml')->__('B'),
            'c' => Mage::helper('adminhtml')->__('C'),
        );
    }

}
