<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_NotificationBar
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_NotificationBar_NotificationbarController extends Mage_Core_Controller_Front_Action {
	
	public function CloseBarAction(){
		$this->loadLayout(false);
		try {
			Mage::getSingleton('core/session')->setSashasBar(1);
		} catch (Exception $e) {
			die($e->getMessage());
		}		 
		exit('success');
	}
	
	public function ClosePopupAction(){
		$this->loadLayout(false);
		try {
			Mage::getSingleton('core/session')->setSashasPopup(1);
		} catch (Exception $e) {
			die($e->getMessage());
		}		 
		exit('success');
	}	
}