<?php
 
class Made_Cloudinary_Model_Resource_Export extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/export', 'cloudinary_export_id');
    }

}