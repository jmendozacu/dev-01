<?php
class Magebuzz_Allcategory_IndexController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){
    $this->loadLayout();
    $this->renderLayout();
  }
  
  public function getCatLevel3Action(){
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $result = array();
    $result['html'] = '';
    
    $cat2Id = $this->getRequest()->getParam('cat2Id');
    $helper = Mage::helper('allcategory');
    $categoryHelper = Mage::helper('catalog/category');
    
    $subCatLvl3 = $helper->getSubcategoriesFromParent($cat2Id);
    if(count($subCatLvl3) > 0){
      $result['success'] = true;
      $_columnCount =4;
      $i=0;
      foreach($subCatLvl3 as $_catItem){
				$cat3 = Mage::getModel('catalog/category')->load($_catItem->getId());
        if ($i++%$_columnCount==0){
          $result['html'] .= '<ul class="level1">';
        }
        $result['html'] .= '<li class="level1">';
					if(isset($cat3['category_icon']) && $cat3['category_icon'] != ''){
						$result['html'] .= '<img class="category-icon" src="'.Mage::getBaseUrl('media').'/catalog/category/'.$cat3['category_icon'].'" />';
					}
          $result['html'] .= '<a href="'.$categoryHelper->getCategoryUrl($cat3).'">'.$cat3->getName().'</a>';
          $subCatLvl4 = $helper->getSubcategoriesFromParent($_catItem->getId());
					if(count($subCatLvl4) > 0){
						$result['html'] .= '<ul class="level2">';
							foreach($subCatLvl4 as $_subcatItem){
								$cat4 = Mage::getModel('catalog/category')->load($_subcatItem->getId());
								$result['html'] .= '<li class="level2">';
									$result['html'] .= '<a href="'.$categoryHelper->getCategoryUrl($cat4).'">'.$cat4->getName().'</a>';
								$result['html'] .= '</li>';
							}
						$result['html'] .= '</ul>';
					}
        $result['html'] .= '</li>';
        if ($i%$_columnCount==0 || $i==count($cat_level_3)){
          $result['html'] .= '</ul>';
        }
      }
    }
    else{
      $result['success'] = false;
    }
    
    $this->getResponse()->setBody(json_encode($result));
  }
}
