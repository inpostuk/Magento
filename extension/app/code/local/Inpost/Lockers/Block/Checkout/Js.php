<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Block_Checkout_Js extends Mage_Core_Block_Template
{

    public function getAddress()
    {
        $shippingAddress = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        $return = array();
        $return['company'] = $shippingAddress->getCompany();
        $return['street'] = $shippingAddress->getStreet();
        $return['city'] = $shippingAddress->getCity();
        $return['postcode'] = $shippingAddress->getPostcode();
        return htmlspecialchars(json_encode($return));
    }

    public function getLocationsCount() {
        $model = Mage::getResourceModel('inpost_lockers/machine');
        $count = ceil($model->getLocationsCount() / 100) * 100;
        
        return number_format($count);
    }

    public function getShippingPrice() {
        return Mage::getStoreConfig('carriers/inpost_lockers/price');
    }
}