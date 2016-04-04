<?php

class Azebiz_CustomContact_Block_Adminhtml_Customcontact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("customcontactGrid");
				$this->setDefaultSort("contact_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("customcontact/customcontact")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("contact_id", array(
				"header" => Mage::helper("customcontact")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "contact_id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("customcontact")->__("Name"),
				"index" => "name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("customcontact")->__("Email"),
				"index" => "email",
				));
				$this->addColumn("telephone", array(
				"header" => Mage::helper("customcontact")->__("Telephone"),
				"index" => "telephone",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('contact_id');
			$this->getMassactionBlock()->setFormFieldName('contact_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_customcontact', array(
					 'label'=> Mage::helper('customcontact')->__('Remove Customcontact'),
					 'url'  => $this->getUrl('*/adminhtml_customcontact/massRemove'),
					 'confirm' => Mage::helper('customcontact')->__('Are you sure?')
				));
			return $this;
		}
			

}