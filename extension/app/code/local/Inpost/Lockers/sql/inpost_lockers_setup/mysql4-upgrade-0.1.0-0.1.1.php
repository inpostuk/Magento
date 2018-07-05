<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$installer->startSetup();

$installer->addAttribute('order', 'locker_id', array('type'=>'varchar','default'=>''));
$installer->addAttribute('quote', 'locker_id', array('type'=>'varchar','default'=>''));

// Refresh DB table describing cache programmatically
if (method_exists($this->_conn, 'resetDdlCache')) {
    $this->_conn->resetDdlCache('sales_flat_order');
    $this->_conn->resetDdlCache('sales_flat_quote');
}

$installer->endSetup();