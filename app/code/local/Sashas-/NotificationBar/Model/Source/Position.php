<?php
/**
 * @author		Sashas
* @category    Sashas
* @package     Sashas_NotificationBar
* @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
* @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
*/

class Sashas_NotificationBar_Model_Source_Position
{

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$positions=array();
		$positions[]=array('value'=>'absolute','label'=>'Overlay Header (Absolute position)');
		$positions[]=array('value'=>'relative','label'=>'Before Header (Relative position)');
		$positions[]=array('value'=>'fixed','label'=>'Sticky Bar (Fixed position)');
		return $positions;
	}

}