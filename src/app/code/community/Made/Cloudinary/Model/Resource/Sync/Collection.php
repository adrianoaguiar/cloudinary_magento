<?php

use CloudinaryAdapter\Export\SyncedMediaRepo;

class Made_Cloudinary_Model_Resource_Sync_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract implements SyncedMediaRepo
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    public function findUnsyncedImages($limit=200)
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/catalog_media_gallery');

        $this->getSelect()
             ->joinRight($tableName, 'value_id=media_gallery_id', '*')
             ->where('id is null')
             ->limit($limit);

        return $this->getItems();
    }
}
