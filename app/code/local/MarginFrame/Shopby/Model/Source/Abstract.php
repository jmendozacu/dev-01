<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
abstract class MarginFrame_Shopby_Model_Source_Abstract extends Varien_Object
{
    abstract public function toOptionArray();

    public function getHash()
    {
        $options = $this->toOptionArray();
        $hash = array();
        foreach ($options as $option) {
            $hash[$option['value']] = $option['label'];
        }
        return $hash;
    }
}