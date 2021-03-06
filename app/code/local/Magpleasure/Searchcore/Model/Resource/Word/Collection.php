<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Searchcore
 */

class Magpleasure_Searchcore_Model_Resource_Word_Collection
    extends Magpleasure_Common_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('searchcore/word');
    }
}