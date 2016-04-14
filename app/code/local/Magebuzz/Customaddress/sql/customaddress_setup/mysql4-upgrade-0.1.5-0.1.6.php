<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->updateAttribute(
    'customer_address',
    'telephone',
    'is_required',
    0
);

$installer->endSetup();
