<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */
class Inpost_Lockers_Block_Adminhtml_Shipping_Package extends Mage_Core_Block_Template
{
    protected $order = false;

    public function isBlockAvailable()
    {
        $order = Mage::registry('current_shipment')->getOrder();
        if (strpos($order->getShippingMethod(), 'inpost_lockers') !== false
            && $order->getLockerId()
        ) {
            return true;
        }
        return false;
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    public function getOrder()
    {
        if (!$this->order) {
            if ($orderId = $this->getOrderId()) {
                $this->order = Mage::getModel('sales/order')->load($orderId);
            }
        }
        return $this->order;
    }

    public function getTotalWeight()
    {
        $order = $this->getOrder();
        $weight = 0;
        foreach ($order->getAllVisibleItems() as $item) {
            $weight += ($item->getWeight() * ((int)$item->getQtyOrdered() - (int)$item->getQtyShipped()));
        }
        return $weight;
    }

    public function getItemsJson()
    {
        $order = $this->getOrder();
        $array = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $array[$item->getId()] = $item->getWeight();
        }
        return json_encode($array);
    }

    public function getSizes()
    {
        $sizes = Mage::getModel('inpost_lockers/system_config_source_size')->toArray();
        return $sizes;
    }

    public function getDefaultSize()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/default_size');
    }
}