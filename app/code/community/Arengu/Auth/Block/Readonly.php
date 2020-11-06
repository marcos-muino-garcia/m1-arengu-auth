<?php

class Arengu_Auth_Block_Readonly extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = parent::_getElementHtml($element);

        return str_replace('<input ', '<input readonly disabled ', $html);
    }
}
