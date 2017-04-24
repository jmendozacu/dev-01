<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_WidgetController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Chooser Source action
	 */
	public function chooserAction()
	{
		$this->getResponse()->setBody(
			$this->_getMagazineBlock()->toHtml()
		);
	}

	protected function _getMagazineBlock()
	{
		return $this->getLayout()
			->createBlock('flippingbook/adminhtml_magazine_widget_chooser',
				'',
				array(
					'id' => $this->getRequest()->getParam('uniq_id')
				)
			);
	}
}
