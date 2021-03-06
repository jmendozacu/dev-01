<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

class Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Renderer_Store
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
	public function render(Varien_Object $row)
	{
		$out = parent::render($row);
		if (empty($out)) {
			return Mage::helper('ammeta')->__('Default');
		}

		return $out;
	}
}
