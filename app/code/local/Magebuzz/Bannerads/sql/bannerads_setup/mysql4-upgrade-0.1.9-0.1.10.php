<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('bannerads_images')} ADD `use_video` smallint(6) NULL default 0;
ALTER TABLE {$this->getTable('bannerads_images')} ADD `url_video` varchar(255) NULL default NULL;
ALTER TABLE {$this->getTable('bannerads_images')} ADD `video_width` FLOAT NULL default NULL;
ALTER TABLE {$this->getTable('bannerads_images')} ADD `video_height` FLOAT NULL default NULL;

");

$installer->endSetup(); 