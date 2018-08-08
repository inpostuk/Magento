<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
    protected $_orders;

    public function checkOrdersCount()
    {
        if (!$this->_orders) {
            $this->getOrders();
        }

        return $this->_orders->count();
    }

    public function getOrders()
    {
        if (!$this->_orders) {
            $this->_orders = Mage::getResourceModel('sales/order_collection')->addFieldToFilter(
                'status',
                array(
                    'in' => array(
                        'inpost_deliveredtoagency',
                        'inpost_missing',
                        'inpost_labelexpired'
                    )
                )
            );
        }

        return $this->_orders;
    }
}