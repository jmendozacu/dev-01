<?php
    /**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */
    $installer = $this;
    $installer->startSetup();
    
    $installer->run("
        ALTER TABLE `{$this->getTable('amfeed/field')}` 
        ADD COLUMN `store_id` INT default 0;
    ");
?>