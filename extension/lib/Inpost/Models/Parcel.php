<?php

/**
 * (c) InPost UK Ltd <it_support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 * 
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

/**
 * Class Parcel.
 *
 * Represents InPost parcel sent to a machine or a POP, picked up by a courier.
 */
class Inpost_Models_Parcel extends Varien_Object
{
    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return Varien_Object
     */
    public function addData(array $arr)
    {
        foreach($arr as $index=>$value) {
            if (is_object($value)) {
                $value = (array)$value;
            }
            $this->setData($index, $value);
        }
        return $this;
    }

    public function preparePhone($phone) {
        $phone = trim($phone);
        $phone = preg_replace('/(^\+44)|(^44)|(^0044)|(^0)|(\s+)/', '', $phone);
        return $phone;
    }
}