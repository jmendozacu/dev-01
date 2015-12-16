<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

$table = $this->getTable('amshopby/filter');
$this->run("ALTER TABLE {$table} CHANGE `block_pos` `block_pos` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'left'");

$this->endSetup();


