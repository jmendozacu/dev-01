<?php
class Magebuzz_CustomContact_Block_Adminhtml_Customcontact_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("customcontact_form", array("legend"=>Mage::helper("customcontact")->__("Comment information")));

				
						$fieldset->addField("contact_id", "text", array(
						"label" => Mage::helper("customcontact")->__("Contact Id"),
						"name" => "contact_id",
						));
					
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("customcontact")->__("Name"),
						"name" => "name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("customcontact")->__("Email"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "email",
						));
					
						$fieldset->addField("telephone", "text", array(
						"label" => Mage::helper("customcontact")->__("Telephone"),
						"name" => "telephone",
						));
					
						$fieldset->addField("comment", "textarea", array(
						"label" => Mage::helper("customcontact")->__("Comment"),
						"name" => "comment",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCustomcontactData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCustomcontactData());
					Mage::getSingleton("adminhtml/session")->setCustomcontactData(null);
				} 
				elseif(Mage::registry("customcontact_data")) {
				    $form->setValues(Mage::registry("customcontact_data")->getData());
				}
				return parent::_prepareForm();
		}
}
