<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
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
        $mathSelect = $this->_getReadAdapter()
            ->select()
            ->from($tableName)
            ->columns(
                "(((acos(sin(($lat*pi()/180)) *
        sin((`latitude`*pi()/180))+cos(($lat*pi()/180)) *
        cos((`latitude`*pi()/180)) * cos((($lng -
        `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance"
            );

        $select = $this->_getReadAdapter()
            ->select()
            ->from($mathSelect)
            ->where("t.distance < $distance")
            ->order('t.distance ASC')
            ->limitPage(0, $limit);

        $locations = $this->getReadConnection()->fetchAssoc($select);
        $items = array();
        foreach ($locations as $location) {
            $location['distance'] = $location['distance'] / 1.60934;
            $item = Mage::getModel('inpost_lockers/machine')->addData($location);
            $items[] = $item;
        }

        return $items;
    }

    public function getLocationsCount()
    {
        $resource = Mage::getSingleton('core/resource');
        $select = $this->_getReadAdapter()
            ->select()
            ->from($resource->getTableName('inpost_lockers/machine'))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('COUNT(machine_id) as count'));
        $count = $this->getReadConnection()->fetchOne($select);
        return $count;
    }

    public function removeMachineById($id)
    {
        if ($id) {
            $resource = Mage::getSingleton('core/resource');
            $this->_getWriteAdapter()
                ->delete(
                    $resource->getTableName('inpost_lockers/machine'),
                    "machine_id=" . $id
                );
        }
    }
}