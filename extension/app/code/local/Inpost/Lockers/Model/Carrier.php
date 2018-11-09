<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'inpost_lockers';


    public function getAllowedMethods()
    {
        return array(
            'standard' => 'InPost 24/7 locker collection (next day) 
            <img src="/skin/frontend/base/default/images/inpost/inpost.png"/>',
        );
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $helper = Mage::helper('inpost_lockers');

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        $expressAvailable = true;
        foreach ($request->getAllItems() as $item) {
            $expressAvailable = false;
        }

        if ($expressAvailable) {
            $result->append($this->_getExpressRate());
        }

        $result->append($this->_getStandardRate($request));

        return $result;
    }

    protected function _getStandardRate(Mage_Shipping_Model_Rate_Request $request)
    {
        $helper = Mage::helper('inpost_lockers');
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethodTitle($this->getConfigData('method'));
        $shippingPrice = $helper->getShippingPrice();

        if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
            $shippingPrice = '0.00';
        }

        $rate->setPrice($shippingPrice);
        $rate->setCost($shippingPrice);

        return $rate;
    }


    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function isTrackingAvailable()
    {
        return true;
    }
}
