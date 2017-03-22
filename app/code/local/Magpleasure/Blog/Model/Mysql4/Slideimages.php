<?php
class Magpleasure_Blog_Model_Mysql4_Slideimages extends Magpleasure_Common_Model_Resource_Abstract
{

  public function _construct()
  {
    parent::_construct();
    $this->_init('mpblog/slideimages','slide_images_id');
  }

}