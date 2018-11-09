<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_Observer
{
    public function controllerActionPredispatchCheckoutOnepage()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote->getLockerId()) {
            $quote->setLockerId('');
            $quote->save();
        }

    }

    public function salesOrderShipmentSaveBefore($observer)
    {
        $shipment = $observer->getShipment();
        $helper = Mage::helper('inpost_lockers');
        $order = $shipment->getOrder();
        if (strpos($order->getShippingMethod(), 'inpost_lockers') !== false &&
            $helper->getCreateLabelsInMagentoFlag()) {
            $request = Mage::app()->getRequest();
            $weight = $request->getParam('weight');
            $size = $request->getParam('size');
            if (!$size) {
                $size = Mage::getStoreConfig('carriers/inpost_lockers/default_size');
            }

            if ($size && $order->getId() && $order->getLockerId()) {
                try {
                    $client = new Inpost_Api_Client(
                        $helper->getToken(),
                        $helper->getEndpoint(),
                        $helper->getMerchantEmail()
                    );
                    $machineId = Mage::helper('inpost_lockers/machine')->getApiMachineIdById($order->getLockerId());
                    $shippingAddress = $order->getShippingAddress();
                    $weight = $this->calculateWeight($weight);
                    /** @var Inpost_Models_Parcel $parcel */
                    $parcel = $client->createParcel(
                        $shippingAddress->getTelephone(),
                        $machineId,
                        $size,
                        $weight,
                        $shippingAddress->getEmail(),
                        $shipment->getOrder()->getIncrementId()
                    );
                    $parcelData = json_encode($parcel->getData());
                    $order->setData('parcel_data', $parcelData);
                    $shipment->setParcelData($parcelData);
                    if ($parcel->getId()) {
                        $helper = Mage::helper('inpost_lockers');
                        $client->pay($parcel->getId());
                        $client->getLabel(
                            $parcel->getId(),
                            $helper->getLabelsPath(),
                            $order->getIncrementId(),
                            $helper->getLabelsFormat()
                        );
                        $order->save();
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), 3, 'inpost.log');
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::throwException($e->getMessage());
                }
            }
        }
    }

    protected function calculateWeight($weight)
    {
        $metric = Mage::getStoreConfig('carriers/inpost_lockers/weight');
        switch ($metric) {
            case 'g':
                $finalWeight = $weight;
                break;
            case 'kg':
                $finalWeight = $weight * 1000;
                break;
            case 'lb':
                $finalWeight = $weight * 453.592;
                break;
            default:
                $finalWeight = $weight;
                break;
        }

        return $finalWeight;
    }

    public function salesOrderShipmentSaveAfter($observer)
    {
        $shipment = $observer->getShipment();
        if ($data = $shipment->getParcelData()) {
            $data = (array)json_decode($data);
            if (array_key_exists('id', $data)) {
                Mage::getModel('sales/order_shipment_track')
                    ->setShipment($shipment)
                    ->setData('title', 'Click and collect InPost')
                    ->setData('number', $data['id'])
                    ->setData('carrier_code', 'inpost')
                    ->setData('order_id', $shipment->getData('order_id'))
                    ->save();
                $order = $shipment->getOrder();
                $order->setStatus('inpost_shipped');
                $order->save();
            }
        }
    }

    public function salesOrderPlaceAfter($observer)
    {
        $order = $observer->getOrder();
        if (strpos($order->getShippingMethod(), 'inpost_lockers') !== false) {
            $locker = Mage::getModel('inpost_lockers/machine')->load($order->getLockerId());
            if ($locker->getId()) {
                $lockerId = $locker->getData('id');
                $address = $order->getShippingAddress();
                $address->setCity($locker->getCity());
                $address->setPostcode($locker->getPostCode());
                $address->setStreet(
                    array(
                        $locker->getStreet(),
                        "Locker ID ($lockerId)"
                    )
                );
                $address->setCompany($locker->getBuildingNo());
                $address->setRegion('');
                $address->setFax('');
                if ($phone = Mage::app()->getRequest()->getParam('locker-phone')) {
                    $address->setTelephone($phone);
                }

                $address->save();
            }
        }
    }

    public function salesOrderPlaceBefore($observer)
    {
        $order = $observer->getOrder();
        if (strpos($order->getShippingMethod(), 'inpost_lockers') === false) {
            $address = $order->getShippingAddress();
            if (strpos(strtolower($address->getCompany()), 'inpost') !== false ||
                strpos(strtolower($address->getStreet(1)), 'inpost') !== false) {
                Mage::throwException(
                    'InPost locker is selected as shipping address with different carrier. 
                    Please amend shipping address or select InPost shipping method.'
                );
            }
        }
    }

    public function checkoutControllerOnepageSaveShippingMethod($observer)
    {
        $params = $observer->getRequest()->getParams();
        if ($params['shipping_method'] == 'inpost_lockers_' && array_key_exists('locker-phone', $params)) {
            $observer->getQuote()->getShippingAddress()->setTelephone($params['locker-phone']);
        }
    }
}