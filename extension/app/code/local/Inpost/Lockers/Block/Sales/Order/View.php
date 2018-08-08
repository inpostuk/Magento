<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Block_Sales_Order_View extends Mage_Sales_Block_Order_View
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('inpost/sales/order/view.phtml');
    }
}