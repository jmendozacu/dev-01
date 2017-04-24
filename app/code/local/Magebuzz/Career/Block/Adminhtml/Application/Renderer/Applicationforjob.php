<?php

class Magebuzz_Career_Block_Adminhtml_Application_Renderer_Applicationforjob extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
  public function render(Varien_Object $row) {
    $getData = $row->getData();
    $application_for_job_id = $getData['application_for_job_id'];
    $jobTitle = Mage::getModel('career/job')->load($application_for_job_id)->getData('title');
    return $jobTitle ;
  }

}