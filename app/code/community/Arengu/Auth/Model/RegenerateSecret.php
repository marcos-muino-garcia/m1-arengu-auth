<?php

class Arengu_Auth_Model_RegenerateSecret extends Mage_Core_Model_Config_Data {
  public function save() {
    $regenerate = $this->getValue();

    if($regenerate) {
      $field = $this->getData('field');
      $helper = Mage::helper('arengu_auth');

      if($field === 'jwt_secret') {
        $helper->regenerateJwtSecret();
      } else if($field === 'api_key') {
        $helper->regenerateApiKey();
      }
    }

    // and then never really save anything for this field, we're
    // only using as a confirmation step to avoid accidental clicks
  }
}
