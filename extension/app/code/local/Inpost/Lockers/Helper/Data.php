<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isActive()
    {
        return (bool)Mage::getStoreConfigFlag('carriers/inpost_lockers/active');
    }

    public function getToken()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/token');
    }

    public function getName()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/name');
    }

    public function getEndpoint()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/endpoint');
    }

    public function isDebug()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/debug');
    }

    public function getGoogleMapsKey()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/map_key');
    }

    public function getShippingPrice()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/price');
    }

    public function getMethodDescription()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/description');
    }


    public function getMerchantEmail()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/email');
    }

    public function getLabelsFormat()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/labels');
    }

    public function getLabelsPath()
    {
        $mediaPath = Mage::getBaseDir('media');
        $path = $mediaPath . DS . 'inpost' . DS . 'shipping-labels';
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($mediaPath . DS . 'inpost');
        $io->checkAndCreateFolder($mediaPath . DS . 'inpost' . DS . 'shipping-labels');
        return $path;
    }

    public function getMethodName()
    {
        return Mage::getStoreConfig('carriers/inpost_lockers/method');
    }

    public function getDefaultRadius()
    {
        if ($radius = Mage::getStoreConfig('carriers/inpost_lockers/map_radius')) {
            return $radius;
        }

        return 10;
    }

    public function getCreateLabelsInMagentoFlag()
    {
        return (bool)Mage::getStoreConfigFlag('carriers/inpost_lockers/parcel_create');
    }

}