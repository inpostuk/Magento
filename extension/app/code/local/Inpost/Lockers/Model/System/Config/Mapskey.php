<?php

class Inpost_Lockers_Model_System_Config_Mapskey extends Mage_Core_Model_Config_Data {

    public function save()
    {
        $collection = Mage::getResourceModel('inpost_lockers/machine_collection');
        if (!$collection->getSize()) {
            Mage::getSingleton('core/session')->addNotice('Please make sure Magento cron is running');
        }

        if (!$this->getValue()) {
            Mage::getSingleton('core/session')->addError('Please provide your Google Maps API Key');
        } else {
            $url = sprintf(
                "https://maps.googleapis.com/maps/api/geocode/xml?address=United Kingdom&key=%s",
                $this->getValue()
            );
            $result = simplexml_load_file($url);

            if (isset($result->status) && $result->status->__toString() !== 'OK') {
                Mage::getSingleton('core/session')->addError('Google Maps Api key is incorrect.');
            }
        }

        return parent::save();
    }

}