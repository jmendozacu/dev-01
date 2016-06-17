<?php

class Amasty_Customform_Block_Adminhtml_Submit_Renderer_Fieldvalue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$datas =  $row->getData($this->getColumn()->getIndex());
        if(!empty($datas)){
            try{
                $datas = unserialize($datas);
            }catch(Exception $e){
                $datas = array();
            }
        }
        $desc = array();
        $i = 0;
        foreach ($datas as $data) {
        	if($i < 4){
        		$desc[] = '<b>' . $data['label'] . '</b> : '.  $data['value'];
        	}
        	$i++;
        }
        return implode('<br>', $desc);
        //return '<span style="color:;">'.implode('<br>', $desc).'</span>';
	}
 
}
