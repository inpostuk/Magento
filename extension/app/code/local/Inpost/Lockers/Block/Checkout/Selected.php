<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */


class Inpost_Lockers_Block_Checkout_Selected extends Mage_Core_Block_Template
{
    protected $_locker = false;

    public function setLocker($locker)
    {
        $this->_locker = $locker;
        return $this;
    }

    public function getCurrentLocker()
    {
        if ($this->_locker) {
            return $this->_locker;
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($id = $quote->getLockerId()) {
            $locker = Mage::getModel('inpost_lockers/machine')->load($id);
            if ($locker->getId()) {
                return $locker;
            }
        }

        return false;
    }

    public function getTelephone()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $phone = $quote->getShippingAddress()->getTelephone();
        if ($phone && $phone !== 'undefined') {
            return $quote->getShippingAddress()->getTelephone();
        } else {
            return $quote->getBillingAddress()->getTelephone();
        }
    }

    public function isActive()
    {
        return Mage::helper('inpost_lockers')->isActive();
    }

    public function getMethodDescription()
    {
        return $this->__(Mage::helper('inpost_lockers')->getMethodDescription());
    }
}