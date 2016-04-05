<?php
$installer = $this;
$installer->startSetup();
try {
    $this->run("
        CREATE TABLE IF NOT EXISTS `contact_form_m` (
            `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `telephone` varchar(255) NOT NULL,
            `comment` text,
            PRIMARY KEY (`contact_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    ");
} catch (Exception $ex) {
    Mage::logException($ex);
}

$installer->endSetup();
	 