<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */


class Inpost_Lockers_Block_Checkout_Checker extends Mage_Core_Block_Template
{
    public function getQuoteId()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getId();
    }
}