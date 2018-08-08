<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Block_Checkout_Onepage_Shipping_Method_Lockers extends Mage_Core_Block_Template
{

    protected $_collection = false;
    protected $_coordinates = array();
    protected $_daysOfWeek = array(
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    );

    public function getGoogleMapsKey()
    {
        return Mage::helper('inpost_lockers')->getGoogleMapsKey();
    }

    public function getPostcode()
    {
        $quote = Mage::registry('current_quote');
        if ($postcode = $quote->getBillingAddress()->getPostcode()) {
            return $postcode;
        }

        return '';
    }

    public function getNearesItems()
    {
        $limit = $this->getRequest()->getParam('limit');
        if (!$limit) {
            $limit = 5;
        }

        $coordinates = Mage::registry('coordinates');

        if ($coordinates) {
            $coordinates = (array)json_decode($coordinates);
            $this->_coordinates = $coordinates;
            $this->_collection = Mage::getResourceModel('inpost_lockers/machine')->getNearestLocations(
                $coordinates['lat'],
                $coordinates['lng'],
                $limit
            );

            $this->registerCollection('machine_collection', $this->_collection);
            return $this->_collection;
        }
    }

    public function registerCollection($key, $collection)
    {
        if (Mage::registry($key)) {
            Mage::unregister($key);
        }

        Mage::register($key, $collection);
    }


    public function getOperatingHours($item)
    {
        $html = '<table class="op-hours">';
        $html .= '<tr><td>Accessible 24/7</td></td></tr>';
        $html .= '<tr><td>' . $item->getLocationDescription() . '</td></tr>';
        $html .= '</table>';
        return $html;
    }

    public function getCoordinate($key)
    {
        $coordinates = (array)json_decode(Mage::registry('coordinates'));
        if (array_key_exists($key, $coordinates)) {
            return $coordinates[$key];
        }

        return '';
    }

    public function getLocations()
    {
        $limit = $this->getRequest()->getParam('limit');
        $locations = Mage::helper('inpost_lockers/locations')->getLocations(
            $this->getCoordinate('lat'),
            $this->getCoordinate('lng'),
            $limit
        );
        return json_encode($locations);
    }

    public function getStoresFound()
    {
        $collection = Mage::helper('inpost_lockers/locations')->getLocations(
            $this->getCoordinate('lat'),
            $this->getCoordinate('lng'),
            20000
        );
        return count($collection);
    }

    public function getPostcodeHiddenValue()
    {
        $postcode = $this->getRequest()->getParam('postcode');
        if ($postcode) {
            return $postcode;
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote->getId()) {
            return $quote->getBillingAddress()->getPostcode();
        }

        return '';
    }

    public function getQuoteId()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getId();
    }

    public function isOnepageCheckout()
    {
        if ($this->getRequest()->getParam('onepage') == 'onepage') {
            return true;
        } else {
            return false;
        }
    }

    public function getAppType()
    {
        if (!$param = $this->getRequest()->getParam('type')) {
            return 'desktop';
        }

        return $param;
    }
}