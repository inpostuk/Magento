<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Lockers_Model_Machine extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('inpost_lockers/machine');
    }

    public function updateAttributes($values)
    {
        $this->addData($values);
        $this->save();
    }

}