<?php

/** @var $this Mage_Catalog_Model_Resource_Setup */
$this->startSetup();

$profileTable = $this->getTable('marginframe_dataflow/profile_import');

$this->getConnection()->addColumn(
    $profileTable,
    'scope',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'default'   => 0,
        'nullable'  => false,
        'comment'   => 'Store View Scope'
    )
);

$this->getConnection()->addColumn(
    $profileTable,
    'can_create_urlkey',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TINYINT,
        'default'   => 0,
        'nullable'  => false,
        'comment'   => 'Can create urlkey'
    )
);

$this->endSetup();