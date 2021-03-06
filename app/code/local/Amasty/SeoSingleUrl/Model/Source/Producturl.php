<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


class Amasty_SeoSingleUrl_Model_Source_Producturl
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$helper = Mage::helper('adminhtml');
		return array(
			array('value' => 0, 'label' => $helper->__('Default Rules')),
			array('value' => 1, 'label' => $helper->__('Shortest Path')),
			array('value' => 2, 'label' => $helper->__('Longest Path'))
		);
	}

}