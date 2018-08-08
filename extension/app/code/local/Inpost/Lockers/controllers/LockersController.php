<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_LockersController extends Mage_Core_Controller_Front_Action
{

    public function getNearestLocationsAction()
    {
        $params = $this->getRequest()->getParams();
        if (array_key_exists('lat', $params) && array_key_exists('lng', $params)) {
            $locations = Mage::getResourceModel('inpost_lockers/machine')->getNearestLocations(
                $params['lat'],
                $params['lng']
            );
        }
    }

    public function getWidgetAction()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        if ($quoteId) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            if ($quote->getId()) {
                $helper = Mage::helper('inpost_lockers');
                // in case billing address hasn't been provided at this point, we'll show map for Camden by default
                // prompting users to enter their post code in the address field
                if ($quote->getBillingAddress()->getPostcode()) {
                    $billingPostcode = $quote->getBillingAddress()->getPostcode();
                } else {
                    if (!$billingPostcode = $this->getRequest()->getParam('postcode')) {
                        $billingPostcode = 'NW1 8AH';
                    }

                    $quote->getBillingAddress()->setPostcode($billingPostcode);
                }

                $url = sprintf(
                    "https://maps.googleapis.com/maps/api/geocode/xml?address=%s,GB&key=%s",
                    $billingPostcode,
                    $helper->getGoogleMapsKey()
                );
                $result = simplexml_load_file($url);
                if ($result->status == 'OK') {
                    Mage::register('current_quote', $quote);
                    $coordinates = array('lat' =>
                        $result->result->geometry->location->lat->__toString(),
                        'lng' => $result->result->geometry->location->lng->__toString()
                    );
                    Mage::register('coordinates', json_encode($coordinates));
                    $response['html'] = $this->getLayoutHtml();
                    $response['center'] = json_encode($coordinates);
                    $this->getResponse()->setBody(json_encode($response));
                } else {
                    $response['error'] = 'Please enter a valid postcode';
                    $this->getResponse()->setBody(json_encode($response));
                }
            }
        }
    }

    protected function getLayoutHtml()
    {
        $layout = Mage::getModel('core/layout');
        $layout->setArea('frontend');
        $update = $layout->getUpdate();
        $update->load(array('shipping_ajax_lockers'));
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    public function updateMapDataAction()
    {
        $response = array();
        $postcode = $this->getRequest()->getParam('postcode');
        $limit = $this->getRequest()->getParam('limit');
        if (!$postcode) {
            $response['error'] = 'Please enter valid postcode.';
        }

        $helper = Mage::helper('inpost_lockers');
        $url = sprintf(
            "https://maps.googleapis.com/maps/api/geocode/xml?address=%s,GB&key=%s",
            $postcode,
            $helper->getGoogleMapsKey()
        );
        $result = simplexml_load_file($url);
        if ($result->status->__toString() !== 'OK') {
            $response['error'] = 'Can\'t find the postcode.';
        }

        if (array_key_exists('error', $response)) {
            $this->getResponse()->setBody(json_encode($response));
            return;
        }

        $coordinates = array(
            'lat' => $result->result->geometry->location->lat->__toString(),
            'lng' => $result->result->geometry->location->lng->__toString()
        );
        Mage::register('coordinates', json_encode($coordinates));
        $response['left'] = $this->getLayout()
            ->createBlock('inpost_lockers/checkout_onepage_shipping_method_lockers')
            ->setTemplate('inpost/checkout/onepage/shipping_method/lockers/left.phtml')
            ->toHtml();
        $response['locations'] = json_encode(
            Mage::helper('inpost_lockers/locations')->getLocations(
                $coordinates['lat'],
                $coordinates['lng'],
                $limit
            )
        );
        $response['center'] = json_encode($coordinates);
        $this->getResponse()->setBody(json_encode($response));
    }

    public function setLockerAction()
    {
        $request = $this->getRequest();
        $quoteId = $request->getParam('quote_id');
        $locker = $request->getParam('id');
        if ($quoteId && $locker) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $locker = Mage::getModel('inpost_lockers/machine')->load($locker);
            if ($quote->getId() && $locker->getId()) {
                $address = $quote->getShippingAddress();
                $address->setCity($locker->getCity());
                $address->setPostcode($locker->getPostCode());
                $address->setStreet($locker->getStreet());
                $address->setCompany($locker->getBuildingNo());
                $address->setRegion('');
                $address->setFax('');
                $address->save();
                $quote->setLockerId($locker->getId());
                $quote->save();
                $response['success'] = true;
                if ($this->getRequest()->getParam('onepage') == '1') {
                    $response['address_block'] = $this->getLayout()
                        ->createBlock("checkout/onepage_progress")
                        ->setTemplate("checkout/onepage/progress/shipping.phtml")
                        ->toHtml();
                }

                $response['selected_locker'] = $this->getLayout()
                    ->createBlock("inpost_lockers/checkout_selected")
                    ->setLocker($locker)
                    ->setTemplate("inpost/checkout/onepage/shipping_method/lockers/selected/locker.phtml")
                    ->toHtml();
                $this->getResponse()->setBody(json_encode($response));
                return;
            }
        }

        $this->getResponse()->setBody(json_encode(array('success' => false)));
    }

    public function checkShippingMethodAction()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $oldAddress = $this->getRequest()->getParam('old_address');
        $phone = $this->getRequest()->getParam('phone');
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        $address = $quote->getShippingAddress();
        $changed = false;
        $response['validation'] = true;
        if ($quote->getLockerId() && strpos($address->getShippingMethod(), 'inpost_lockers') === false) {
            $oldAddress = (array)json_decode($oldAddress);
            $address->setCompany($oldAddress['company']);
            $address->setStreet($oldAddress['street']);
            $address->setCity($oldAddress['city']);
            $address->setPostcode($oldAddress['postcode']);
            $quote->setLockerId('');
            $quote->save();
            $changed = true;
        }

        if (strpos($address->getShippingMethod(), 'inpost_lockers') !== false && !$quote->getLockerId()) {
            $response['validation'] = false;
        }

        if ($phone && $phone !== 'undefined') {
            $address->setTelephone($phone);
            $changed = true;
        }

        if ($changed) {
            $address->save();
        }

        $response['address_block'] = $this->getLayout()
            ->createBlock("checkout/onepage_progress")
            ->setTemplate("checkout/onepage/progress/shipping.phtml")
            ->toHtml();
        $this->getResponse()->setBody(json_encode($response));
    }

}