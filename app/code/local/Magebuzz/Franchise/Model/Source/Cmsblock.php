<?php
class Magebuzz_Franchise_Model_Source_Cmsblock{
    public function toOptionArray()
    {
        $blocks = array();
        $blockCollections = Mage::getModel('cms/block')->getCollection();
        foreach ($blockCollections as $blockCollection ) {
            $identifier = $blockCollection->getData('identifier');
            $title = $blockCollection->getData('title');
            $blocks[]= array(
                'value' => $identifier,
                'label' => $title
            );

        }
        return $blocks;
    }
}