<?php
	
class Magebuzz_CustomContact_Block_Adminhtml_Customcontact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "contact_id";
				$this->_blockGroup = "customcontact";
				$this->_controller = "adminhtml_customcontact";
				$this->_updateButton("save", "label", Mage::helper("customcontact")->__("Save Comment"));
				$this->_updateButton("delete", "label", Mage::helper("customcontact")->__("Delete Comment"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("customcontact")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("customcontact_data") && Mage::registry("customcontact_data")->getId() ){

				    return Mage::helper("customcontact")->__("Edit Comment '%s'", $this->htmlEscape(Mage::registry("customcontact_data")->getId()));

				} 
				else{

				     return Mage::helper("customcontact")->__("Add Comment");

				}
		}
}