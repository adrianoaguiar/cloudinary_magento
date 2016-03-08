<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('made_cloudinary/export'))
    ->addColumn('cloudinary_export_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => 1
    ), 'Cloudinary Export ID')
    ->addColumn('started', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
    ), 'Export Started');

$installer->getConnection()->createTable($table);

$installer->endSetup();