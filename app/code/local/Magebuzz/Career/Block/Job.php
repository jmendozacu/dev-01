<?php
class Magebuzz_Career_Block_Job extends Mage_Core_Block_Template{
    public function __construct()
    {
        parent::__construct();
        $collection = Mage::getModel('career/job')->getCollection();
        $collection->addFieldToFilter('status','1');
        $this->setCollection($collection);
    }
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setCollection($this->getCollection());
        $pager->setAvailableLimit(array(20=>20,25=>25,30=>30,50=>50));
        $this->setChild('pager', $pager);
        return $this;
    }
    public function getToolbarHtml()
    {
        return $this->getChildHtml('pager');
    }
}