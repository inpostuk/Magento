<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_Cron
{
    protected $finalStatuses = array(
        'delivered',
        'deliveredtoagency',
        'cancelled',
        'claimed'
    );

    protected $mapping = array(
        'delivered' => array(
            'state' => 'complete',
            'status' => 'inpost_delivered'
        ),
        'delivered_to_agency' => array(
            'state' => 'complete',
            'status' => 'inpost_deliveredtoagency'
        ),
        'cancelled' => array(
            'state' => 'complete',
            'status' => 'inpost_cancelled'
        ),
        'claimed' => array(
            'state' => 'complete',
            'status' => 'inpost_claimed'
        ),
        'created' => array(
            'state' => 'complete',
            'status' => 'inpost_shipped'
        ),
        'prepared' => array(
            'state' => 'complete',
            'status' => 'inpost_shipped'
        ),
        'sent' => array(
            'state' => 'complete',
            'status' => 'inpost_shipped'
        ),
        'in_transit' => array(
            'state' => 'complete',
            'status' => 'inpost_shipped'
        ),
        'stored' => array(
            'state' => 'complete',
            'status' => 'inpost_shipped'
        ),
        'avizo' => array(
            'state' => 'complete',
            'status' => 'inpost_stored'
        ),
        'expired' => array(
            'state' => 'complete',
            'status' => 'inpost_expired'
        ),
        'retuned_to_agency' => array(
            'state' => 'complete',
            'status' => 'inpost_returnedtoagency'
        ),
        'label_expired' => array(
            'state' => 'complete',
            'status' => 'inpost_labelexpired'
        ),
        'not_delivered' => array(
            'state' => 'complete',
            'status' => 'inpost_notdelivered'
        ),
        'missing' => array(
            'state' => 'complete',
            'status' => 'inpost_missing'
        )
    );

    public function getMachinesFromApi()
    {
        $helper = Mage::helper('inpost_lockers');
        $client = new Inpost_Api_Client($helper->getToken());
        $machines = $client->getMachinesList();
        foreach ($machines as $machine) {
            if ($machine->getData('status') == 'Operating') {
                if ($machine->getData('id')) {
                    $flag = false;
                    $model = Mage::getResourceModel('inpost_lockers/machine_collection')
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('id', $machine['id'])
                        ->getFirstItem();
                    if ($model->getId()) {
                        foreach ($machine->getData() as $key => $value) {
                            if ($model->getData('key') !== $value) {
                                $model->setData($key, $value);
                                $flag = true;
                            }
                        }
                    } else {
                        $model->addData($machine->getData());
                        $flag = true;
                    }
                    if ($flag) {
                        $model->save();
                    }
                }
            } else {
                $model = Mage::getResourceModel('inpost_lockers/machine_collection')
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('id', $machine['id'])
                    ->getFirstItem();
                if ($model->getId()) {
                    $model->delete();
                }
            }
        }
    }

    public function updateInpostOrderStatuses()
    {
        $now = Mage::getModel('core/date')->timestamp(time());

        $fromDate = date('Y-m-d' . ' 00:00:00', strtotime('-7 day'));
        $toDate = date('Y-m-d' . ' 23:59:59', $now);
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('status', array('in' => array('inpost_shipped', 'inpost_delivered', 'inpost_stored', 'inpost_expired', 'inpost_returnedtoagency', 'inpost_labelexpired',
                'inpost_notdelivered', 'inpost_missing')))
            ->addFieldToFilter('created_at', array(
                'from' => $fromDate,
                'to' => $toDate,
                'date' => true,
            ))
            ->addFieldToFilter('shipping_method', array('like' => '%inpost_lockers%'));
        $helper = Mage::helper('inpost_lockers');
        $client = new Inpost_Api_Client($helper->getToken(), $helper->getEndpoint());
        $lastNotFinal = false;
        $counter = array(
            'avizo' => array(
                'counter' => 0,
                'search' => 'parcels that haven’t been picked up by customers',
                'message' => 'InPost: %s parcels that haven’t been picked up by customers from lockers yet (InPost Stored24)'
            ),
            'expired' => array(
                'counter' => 0,
                'search' => 'parcels that have expired',
                'message' => 'InPost: %s parcels that have expired (InPost Expired)'
            ),
            'delivered_to_agency' => array(
                'counter' => 0,
                'search' => 'parcels that have been returned',
                'message' => 'InPost: %s parcels that have been returned (InPost Delivered2Agency)'
            ),
            'missing' => array(
                'counter' => 0,
                'search' => 'missing parcels',
                'message' => 'InPost: %s missing parcels (InPost Missing)'
            ),
            'label_expired' => array(
                'counter' => 0,
                'search' => 'parcels with labels expired',
                'message' => 'InPost: %s parcels with labels expired (InPost LabelExpired)'
            ),
        );
        foreach ($collection as $order) {
            foreach ($order->getShipmentsCollection() as $shipment) {
                $parcelData = (array)json_decode($shipment->getParcelData());
                if (array_key_exists('id', $parcelData)) {
                    $parcelDataFromApi = (array)$client->getParcelData($parcelData['id']);
                    if (array_key_exists('status', $parcelDataFromApi)) {
                        if (array_key_exists($parcelDataFromApi['status'], $counter)) {
                            $counter[$parcelDataFromApi['status']]['counter']++;
                        }
                        if (!array_key_exists($parcelDataFromApi['status'], $this->finalStatuses)) {
                            $lastNotFinal = $parcelDataFromApi;
                        }
                        $json = json_encode($parcelDataFromApi);
                        if ($shipment->getParcelData() !== $json) {
                            $shipment->setParcelData($json)->save();
                        }
                    }
                }
            }
            if (!$lastNotFinal) {
                $lastNotFinal = $parcelDataFromApi;
            }
            $data = $this->mapping[$lastNotFinal['status']];
            $order->setStatus($data['status']);
            $order->addStatusHistoryComment('', $data['status']);
            $order->save();
            foreach ($counter as $key => $value) {
                if ($value['counter'] > 0) {
                    $notification = Mage::getResourceModel('adminnotification/inbox_collection')
                        ->addFieldToFilter('title', array('like' => "%{$value['search']}%"))
                        ->getFirstItem();
                    $notification->setTitle(sprintf($value['message'], $value['counter']));
                    $notification->setSeverity(4);
                    $notification->save();
                }
            }
        }
    }
}