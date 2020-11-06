<?php

class Arengu_Auth_Model_Readonly extends Mage_Core_Model_Config_Data {
    public function save() {
        return $this; // save nothing
    }
}
