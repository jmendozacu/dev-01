<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Model_Mysql4_Slideimages_Collection extends Magpleasure_Blog_Model_Mysql4_Abstract_Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mpblog/slideimages');
    }
}