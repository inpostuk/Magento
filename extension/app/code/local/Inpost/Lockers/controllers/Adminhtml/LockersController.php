<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Adminhtml_LockersController extends Mage_Adminhtml_Controller_Action {

    public function createParcelAction() {
        $request = $this->getRequest();
        $weight = $request->getParam('weight');
        $size = $request->getParam('size');
        $orderId = $request->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $response['error'] = true;
        if ($weight && $size && $orderId && $order->getId() && $order->getLockerId()) {
            $helper = Mage::helper('inpost_lockers');
            try {
                $client = new Inpost_Api_Client($helper->getToken(), $helper->getEndpoint(), $helper->getMerchantEmail());
                $machineId = Mage::helper('inpost_lockers/machine')->getApiMachineIdById($order->getLockerId());
                $shippingAddress = $order->getShippingAddress();
                /** @var Inpost_Models_Parcel $parcel */
                $parcel = $client->createParcel($shippingAddress->getTelephone(), $machineId, $size, $weight);
                $order->setData('parcel_data', json_encode($parcel->getData()));
                if ($parcel->getId()) {
                    $helper = Mage::helper('inpost_lockers');
                    $order->save();
                    $client->pay($parcel->getId());
                    $client->getLabel($parcel->getId(), $helper->getLabelsPath(), $helper->getLabelsFormat());
                    $response = array(
                        'id' => $parcel->getId(),
                        'receiver_phone' => $parcel->getReceiverPhone(),
                        'target_machine_id' => $parcel->getTargetMachineId(),
                        'service' => $parcel->getService(),
                        'error' => false
                    );
                }
            } catch (Exception $e) {
                Mage::log($e, 3, 'inpost.log');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
        return;
    }
}