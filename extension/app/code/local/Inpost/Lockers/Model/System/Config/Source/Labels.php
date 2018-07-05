<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_System_Config_Source_Labels
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'pdf', 'label'=>Mage::helper('adminhtml')->__('PDF')),
            array('value' => 'zpl', 'label'=>Mage::helper('adminhtml')->__('ZPL')),
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
            'pdf' => Mage::helper('adminhtml')->__('ZPL'),
            'zpl' => Mage::helper('adminhtml')->__('PDF'),
        );
    }

}
