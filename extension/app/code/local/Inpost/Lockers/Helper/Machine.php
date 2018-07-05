<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Helper_Machine extends Mage_Core_Helper_Abstract
{
    public function getApiMachineIdById($id) {
        $model = Mage::getModel('inpost_lockers/machine')->load($id);
        if ($model->getId()) {
            return $model->getData('id');
        }
        return false;
    }
}