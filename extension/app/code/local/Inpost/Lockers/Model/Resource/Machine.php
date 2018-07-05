<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 * 
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_Resource_Machine extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('inpost_lockers/machine', 'machine_id');
    }

    public function getNearestLocations($lat, $lng, $limit = 5, $distance = false)
    {
        $resource = Mage::getSingleton('core/resource');
        $tableName = $resource->getTableName('inpost_lockers/machine');

        if (!$distance) {
            $distance = Mage::helper('inpost_lockers')->getDefaultRadius();
        }

        /** miles to kilometers */
        $distance = $distance * 1.60934;

        $sql = "SELECT * 
        FROM (SELECT *, (((acos(sin(($lat*pi()/180)) *
        sin((`latitude`*pi()/180))+cos(($lat*pi()/180)) *
        cos((`latitude`*pi()/180)) * cos((($lng -
        `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
        FROM `$tableName`) $tableName 
        WHERE distance < $distance
        GROUP BY distance 
		ASC
        LIMIT 0, $limit";
        $locations = $this->getReadConnection()->fetchAssoc($sql);
        $items = array();
        foreach ($locations as $location) {
            $location['distance'] = $location['distance'] / 1.60934;
            $item = Mage::getModel('inpost_lockers/machine')->addData($location);
            $items[] = $item;
        }
        return $items;
    }

    public function getLocationsCount() {
        $resource = Mage::getSingleton('core/resource');
        $tableName = $resource->getTableName('inpost_lockers/machine');
        $sql = "SELECT COUNT(machine_id) as count FROM $tableName";
        $count = $this->getReadConnection()->fetchRow($sql);
        return $count['count'];
    }
}