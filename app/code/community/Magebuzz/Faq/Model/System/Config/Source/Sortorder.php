<?php
class Magebuzz_Faq_Model_System_Config_Source_Sortorder
{

	public function toOptionArray()
	{
		return array(
			'ASC' => Mage::helper('adminhtml')->__('Asc'),
			"DESC" => Mage::helper('adminhtml')->__('Desc')
		);
	}
}
