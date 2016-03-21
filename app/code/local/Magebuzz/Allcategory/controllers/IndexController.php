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
    
    $cat_level_3 = $helper->getCatChildByGroup($cat2Id);
    if(count($cat_level_3)){
      $result['success'] = true;
      $result['cats_3'] = $cat_level_3;
      $_columnCount =4;
      $i=0;
      foreach($result['cats_3'] as $cat3){
        if ($i++%$_columnCount==0){
          $result['html'] .= '<ul class="level1">';
        }
        $result['html'] .= '<li class="level1">';
          $result['html'] .= '<a href="'.Mage::getUrl($cat3['url_path']).'">'.$cat3['name'].'</a>';
          $cat_level_4 = $helper->getCatChildByGroup($cat3['entity_id']);
          $result['html'] .= '<ul class="level2">';
            foreach($cat_level_4 as $cat4){
              $result['html'] .= '<li class="level2">';
                $result['html'] .= '<a href="'.Mage::getUrl($cat4['url_path']).'">'.$cat4['name'].'</a>';
              $result['html'] .= '</li>';
            }
          $result['html'] .= '</ul>';
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
