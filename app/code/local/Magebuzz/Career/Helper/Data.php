<?php
class Magebuzz_Career_Helper_Data extends Mage_Core_Helper_Abstract{
  public function getJob(){
    $collection = Mage::getModel('career/job')->getCollection();
    $collection->addFieldToFilter('status','1');
    return $collection;
  }
  public function sendEmail($params, $job_title)
  {
    $storeId = Mage::app()->getStore()->getStoreId();

    if(Mage::getStoreConfig('career/general/enable',$storeId)){
      $sender  = array(
        'name'  => Mage::getStoreConfig('trans_email/ident_sales/name'),
        'email' => Mage::getStoreConfig('trans_email/ident_sales/email')
      );
    $receptionEmail = 'recruitment@indexlivingmall.com';
      $receptionName = 'Index-Corporate';

      $emailTemplate = Mage::getStoreConfig('career/general/template_email',$storeId);

      $postObject = new Varien_Object();
      $postObject->setData($params);
      $postObject->setData('jobTitle',$job_title);

      $translate = Mage::getSingleton('core/translate');
      $translate->setTranslateInline(false);
      Mage::getModel('core/email_template')
        ->sendTransactional(
          $emailTemplate,
          $sender,
          $receptionEmail,
          $receptionName,
         array('data' => $postObject)
        );
      $translate->setTranslateInline(true);
    }
  }
}