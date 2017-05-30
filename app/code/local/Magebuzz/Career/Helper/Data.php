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
  public function getWorkType(){
    $collection = Mage::getModel('career/worktype')->getCollection();
    $collection->addFieldToFilter('status','1');
    return $collection;
  }
  public function getAge() {
    return array(
      array('value' => 1, 'label' => Mage::helper('career')->__('0')),
      array('value' => 2, 'label' => Mage::helper('career')->__('0-2')),
      array('value' => 3, 'label' => Mage::helper('career')->__('1-3')),
      array('value' => 4, 'label' => Mage::helper('career')->__('2-5')),
      array('value' => 5, 'label' => Mage::helper('career')->__('3-5')),
      array('value' => 6, 'label' => Mage::helper('career')->__('5-8')),
      array('value' => 7, 'label' => Mage::helper('career')->__('5-10')),
      array('value' => 8, 'label' => Mage::helper('career')->__('8-15')),
      array('value' => 9, 'label' => Mage::helper('career')->__('10-15')),
      array('value' => 10, 'label' => Mage::helper('career')->__('15-25')),
      array('value' => 11, 'label' => Mage::helper('career')->__('20 and up')),
    );
  }
  public function getTitleAge($ageId){
    if($ageId == null){
      return null;
    }
    if($ageId == 1){
      return 0;
    }
    elseif($ageId == 2){
      return '0-2';
    }
    elseif($ageId == 3){
      return '1-3';
    }
    elseif($ageId == 4){
      return '2-5';
    }
    elseif($ageId == 5){
      return '3-5';
    }
    elseif($ageId == 6){
      return '5-8';
    }
    elseif($ageId == 7){
      return '5-10';
    }
    elseif($ageId == 8){
      return '8-15';
    }
    elseif($ageId == 9){
      return '10-15';
    }
    elseif($ageId == 10){
      return '15-25';
    }
    elseif($ageId == 11){
      return '20 and up';
    }
  }
}