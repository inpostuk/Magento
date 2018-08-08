<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

$installer = $this;

$installer->startSetup();


$table = $installer->getConnection()
    ->newTable($installer->getTable('inpost_lockers/machine'))
    ->addColumn(
        'machine_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Machine ID'
    )
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        10,
        array(),
        'Api ID'
    )
    ->addColumn(
        'post_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ), 'Postcode'
    )
    ->addColumn(
        'province',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ), 'Province'
    )
    ->addColumn(
        'street',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '2M',
        array(),
        'Street'
    )
    ->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'City'
    )
    ->addColumn(
        'building_no',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Building NO'
    )
    ->addColumn(
        'flat_no',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null, array(),
        'Flat NO'
    )
    ->addColumn(
        'address_str',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Address street'
    )
    ->addColumn(
        'functions',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Functions'
    )
    ->addColumn(
        'location',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Location'
    )
    ->addColumn(
        'latitude',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'latitude'
    )
    ->addColumn(
        'longitude',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        '$longitude'
    )
    ->addColumn(
        'location_description',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Location description'
    )
    ->addColumn(
        'location_description1',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Location description 1'
    )
    ->addColumn(
        'location_description2',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Location description 2'
    )
    ->addColumn(
        'operating_hours',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Operationg Hours'
    )
    ->addColumn(
        'payment_type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Payment type'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Status'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Type'
    )
    ->addColumn(
        'minimap',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Minimap'
    )
    ->addColumn(
        'self',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Self Link'
    )
    ->addIndex(
        $installer->getIdxName(
            'inpost_lockers/machine',
            array('id')
        ),
        array('id')
    )
    ->setComment('Inpost Machines');
$installer->getConnection()->createTable($table);
Mage::getModel('inpost_lockers/cron')->getMachinesFromApi();

$installer->endSetup();