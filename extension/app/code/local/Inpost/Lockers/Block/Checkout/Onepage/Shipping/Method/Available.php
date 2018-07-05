<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */


class Inpost_Lockers_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{

    public function getShippingRates()
    {

        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $helper = Mage::helper('inpost_lockers');
            if (!$helper->isActive() || $this->getTemplate() !== 'inpost/checkout/onepage/shipping_method/available.phtml') {
                if (array_key_exists('inpost_lockers', $groups)) {
                    unset($groups['inpost_lockers']);
                }
            }

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }
}