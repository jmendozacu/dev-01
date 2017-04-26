<?php

class Mageplace_Flippingbook_Block_Frontend_List extends Mage_Core_Block_Template{

  public function getLastestBook(){
    $model = Mage::getModel('flippingbook/magazine')->getCollection()
      ->addFieldToFilter('is_active', 1)
      ->setOrder('created_at', 'desc')
      ->getFirstItem();
    return $model;
  }

  function formatSizeUnits($bytes)
  {
    if ($bytes >= 1073741824)
    {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
      $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
      $bytes = $bytes . ' byte';
    }
    else
    {
      $bytes = '0 bytes';
    }

    return $bytes;
  }

  public function getPdfUrl($_magazine)
  {
    return Mage::helper('flippingbook')->getPathUrl('pdf').'/'.$_magazine->getMagazineBackgroundPdf();
  }
  public function getMagazineUrl($magazine)
  {
    return Mage::helper('flippingbook')->getMagazineUrl($magazine);
  }



}