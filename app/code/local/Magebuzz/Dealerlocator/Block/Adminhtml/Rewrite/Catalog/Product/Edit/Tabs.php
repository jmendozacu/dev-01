<?php
class Magebuzz_Dealerlocator_Block_Adminhtml_Rewrite_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs{ 
  protected function _prepareLayout(){
    parent::_prepareLayout();
    
    //add new tab
		$params = $this->getRequest()->getParams();
		
		if(isset($params['type'])){
			$url = Mage::helper('adminhtml')->getUrl('dealerlocatoradmin/adminhtml_productassigndealer/index', array('_current' => true));
			$this->addTab('dealer', array(
										'label'     => Mage::helper('catalog')->__('Dealer'),
										'url'       => $url,
										'class'     => 'ajax',
			));    
		}
    
    return parent::_prepareLayout();
  }
}
