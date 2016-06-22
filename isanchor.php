<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load Up Magento Core
define('MAGENTO', realpath(''));

require_once(MAGENTO . '/app/Mage.php');
// echo MAGENTO;
$app = Mage::app();
Mage::app()->setCurrentStore(0);
$categories = Mage::getModel('catalog/category')
 ->getCollection()
 // ->addAttributeToSelect('*')
 // ->addAttributeToFilter('is_anchor', 0)
 // ->addAttributeToFilter('entity_id', array("gt" => 1))
 ->setOrder('entity_id')
 ;

foreach($categories as $category) {
 echo $category->getId() . "\t" . $category->getName() . "<br>";
 $category->setIsAnchor('1');

 $category->save();
}

