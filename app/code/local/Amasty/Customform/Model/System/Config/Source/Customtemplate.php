<?php
class Amasty_Customform_Model_System_Config_Source_Customtemplate
{
  const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';
  public function toOptionArray()
  {
    $result = array();
    $collection = Mage::getResourceModel('core/email_template_collection')
      ->load();
    $options = $collection->toOptionArray();
    foreach ($options as $v) {
      $result[$v['value']] = $v['label'];
    }
    // sort by names alphabetically
    asort($result);
      $options = array();
      $options[] = array('value' => '', 'label' => '---------Choose Email Template---------');
      foreach ($result as $k => $v) {
        if ($k == '')
          continue;
        $options[] = array('value' => $k, 'label' => $v);
      }

      $result = $options;
    return $result;
  }
}

