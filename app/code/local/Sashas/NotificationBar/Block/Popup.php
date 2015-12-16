<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_NotificationBar
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_NotificationBar_Block_Popup extends Mage_Core_Block_Template {
	
	public function ShowBar() {
		$show_bar=1;
		 
		$session_show=Mage::getSingleton('core/session')->getSashasPopup();
		$use_session=Mage::getStoreConfig('notificationbar/notificationbar_group/popup_session',Mage::app()->getStore());
		$enable_extension=Mage::getStoreConfig('notificationbar/notificationbar_group/extension_enabled',Mage::app()->getStore());
		$enable_bar=Mage::getStoreConfig('notificationbar/notificationbar_group/show_popup',Mage::app()->getStore());
		 
		$use_scheduler=Mage::getStoreConfig('notificationbar/notificationbar_group/popup_calendar',Mage::app()->getStore());
		$date_from=Mage::getStoreConfig('notificationbar/notificationbar_group/popup_calendar_from',Mage::app()->getStore());
		$date_to=Mage::getStoreConfig('notificationbar/notificationbar_group/popup_calendar_to',Mage::app()->getStore());
	
		if ($use_session && $session_show)
			$show_bar=0;
		 
		if (!$enable_extension || !$enable_bar)
			$show_bar=0;
			
		if ($use_scheduler) {
			$date_from_timestamp=strtotime($date_from);
			$date_to_timestamp=strtotime($date_to.' 23:59:59');
			$today_timestamp=Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore());
		 	
			if ($date_from_timestamp > $today_timestamp || $today_timestamp > $date_to_timestamp)
				$show_bar=0;
		}
		 
		return  $show_bar;
	}
	
	public function useSession(){
		return Mage::getStoreConfig('notificationbar/notificationbar_group/popup_session',Mage::app()->getStore());
	}
	public function getColor(){
		return Mage::getStoreConfig('notificationbar/notificationbar_group/popup_color',Mage::app()->getStore());
	}
	
 
	public function getCss(){
		return Mage::getStoreConfig('notificationbar/notificationbar_group/popup_custom_css',Mage::app()->getStore());
	}
	
	public function getPopupHtml(){
		return Mage::getStoreConfig('notificationbar/notificationbar_group/popup_custom_html',Mage::app()->getStore());
	}
	
	public function getPopupText(){
		return Mage::getStoreConfig('notificationbar/notificationbar_group/popup_text',Mage::app()->getStore());
	}	

}