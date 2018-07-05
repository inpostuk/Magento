<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Helper_Locations extends Mage_Core_Helper_Abstract {

    public function getLocations($lat, $lng, $limit = 5) {
        if (!$limit) {
            $limit = 5;
        }
        $collection = Mage::getResourceModel('inpost_lockers/machine')->getNearestLocations(
            $lat,
            $lng,
            $limit
        );
        $locations = array();
        foreach ($collection as $item) {
            if (($lat = $item->getLatitude()) && ($lon = $item->getLongitude())) {
                $object = new stdClass();
                $object->id = $item->getId();
                $object->lat = floatval($lat);
                $object->lng = floatval($lon);
                $object->name = preg_replace("|inpost locker-|i",  '', $item->getBuildingNo());
                $object->address = '<p>' . $item->getStreet() . '</p>' . '<p>' . $item->getCity() . ', ' . $item->getPostCode() . '</p>';
                $object->distance = $item->getDistance();
                $locations[] = $object;
            }
        }
        return $locations;
    }
}