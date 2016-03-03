<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Made_Cloudinary_Model_Resource_Synchronisation_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
    implements SynchronizedMediaRepository
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/synchronisation');
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

    public function findUnsynchronisedImages($limit=200)
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/catalog_media_gallery');

        $this->getSelect()
             ->joinRight($tableName, 'value_id=media_gallery_id', '*')
             ->where('cloudinary_synchronisation_id is null')
             ->limit($limit)
        ;

        return $this->getItems();
    }
}