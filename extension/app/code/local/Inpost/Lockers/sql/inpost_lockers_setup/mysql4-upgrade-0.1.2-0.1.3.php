<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

$installer = $this;

// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');

// Insert statuses
$installer->getConnection()->insertArray(
    $statusTable,
    array(
        'status',
        'label'
    ),
    array(
        array('status' => 'inpost_delivered', 'label' => 'InPost Delivered'),
        array('status' => 'inpost_stored', 'label' => 'InPost Stored24'),
        array('status' => 'inpost_expired', 'label' => 'InPost Expired'),
        array('status' => 'inpost_returnedtoagency', 'label' => 'InPost Returned2Agency'),
        array('status' => 'inpost_deliveredtoagency', 'label' => 'InPost Delivered2Agency'),
        array('status' => 'inpost_labelexpired', 'label' => 'InPost LabelExpired'),
        array('status' => 'inpost_notdelivered', 'label' => 'InPost NotDelivered'),
        array('status' => 'inpost_claimed', 'label' => 'InPost Claimed'),
        array('status' => 'inpost_missing', 'label' => 'InPost Missing'),
        array('status' => 'inpost_cancelled', 'label' => 'InPost Cancelled')
    )
);

// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
    $statusStateTable,
    array(
        'status',
        'state',
        'is_default'
    ),
    array(
        array(
            'status' => 'inpost_delivered',
            'state' => 'complete',
            'is_default' => 0
        ),
        array(
            'status' => 'inpost_cancelled',
            'state' => 'complete',
            'is_default' => 0
        ),
        array(
            'status' => 'inpost_stored',
            'state' => 'complete',
            'is_default' => 0
        ),
        array(
            'status' => 'inpost_expired',
            'state' => 'complete',
            'is_default' => 0
        ),
        array(
            'status' => 'inpost_returnedtoagency',
            'state' => 'complete',
            'is_default' => 0
        ),array(
            'status' => 'inpost_deliveredtoagency',
            'state' => 'complete',
            'is_default' => 0
        ),array(
            'status' => 'inpost_labelexpired',
            'state' => 'complete',
            'is_default' => 0
        ),array(
            'status' => 'inpost_notdelivered',
            'state' => 'complete',
            'is_default' => 0
        ),array(
            'status' => 'inpost_claimed',
            'state' => 'complete',
            'is_default' => 0
        ),array(
            'status' => 'inpost_missing',
            'state' => 'complete',
            'is_default' => 0
        )
    )
);