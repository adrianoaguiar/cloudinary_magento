<?php
 
class Made_Cloudinary_Model_Resource_Sync extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync', 'id');
    }


}