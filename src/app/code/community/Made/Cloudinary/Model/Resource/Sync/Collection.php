<?php

use CloudinaryExtension\Export\SyncedMediaRepo;

class Made_Cloudinary_Model_Resource_Sync_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
    implements SyncedMediaRepo
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    protected function _getResource()
    {
        return parent::getResource();
    }

    protected function _getConnection()
    {
        $resource = $this->_getResource();

        return $resource->getReadConnection();
    }

    protected function _getMainTable()
    {
        $resource = $this->_getResource();

        return $resource->getMainTable();
    }

    public function findUnsyncedImages($limit=200)
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/catalog_media_gallery');

        $this->getSelect()
             ->joinRight($tableName, 'value_id=media_gallery_id', '*')
             ->where('cloudinary_sync_id is null')
             ->limit($limit)
        ;

        return $this->getItems();
    }
}