<?php
class Magebuzz_Career_Block_Adminhtml_Application_View extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('career/application/view.phtml');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    public function getSubmit() {
        $submit = Mage::registry('application_current_submit');

        return $submit;
    }

    public function getValuesData() {
        $values = $this->getSubmit()->getData();
        unset($values['application_id']);
        return $values;
    }

    protected function _isAllowed($action) {
        return Mage::getSingleton('admin/session')->isAllowed($action);
    }
}