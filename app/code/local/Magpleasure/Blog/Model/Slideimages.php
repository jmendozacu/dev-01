<?php
class Magpleasure_Blog_Model_Slideimages extends Mage_Core_Model_Abstract{
  public function _construct()
  {
    parent::_construct();
    $this->_init('mpblog/slideimages');
  }
}