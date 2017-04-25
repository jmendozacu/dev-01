<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */

if(Mage::helper('flippingbook/version')->isEE()) {
	class Mageplace_Flippingbook_Helper_Data extends Mageplace_Flippingbook_Helper_Enterprise
	{
	}
} else {
	class Mageplace_Flippingbook_Helper_Data extends Mageplace_Flippingbook_Helper_Community
	{
	}
}
