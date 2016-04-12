<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$setup->removeAttribute("customer_address", "customer_mobile");
$installer->endSetup();
