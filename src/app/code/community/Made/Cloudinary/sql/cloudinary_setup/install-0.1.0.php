<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('made_cloudinary/sync'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Cloudinary Sync ID')
    ->addColumn('media_gallery_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default' => null,
    ), 'Product Media Gallery ID')
    ->addColumn('media_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addForeignKey(
        'FK_MEDIA_GALLERY_ID_VALUE_ID',
        'media_gallery_id',
        $installer->getTable('catalog_product_entity_media_gallery'),
        'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();