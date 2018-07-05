<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
    protected $orders;

    public function checkOrdersCount()
    {
        if (!$this->orders) {
            $this->getOrders();
        }
        return $this->orders->count();
    }

    public function getOrders()
    {
        if (!$this->orders) {
            $this->orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('status', array('in' => array('inpost_deliveredtoagency', 'inpost_missing', 'inpost_labelexpired')
                ));
        }
        return $this->orders;
    }

}