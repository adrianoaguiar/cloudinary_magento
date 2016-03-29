<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Made_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    protected function _afterSave($result)
    {
        parent::_afterSave($result);

        if (!empty($result['path']) && !empty($result['file'])) {
            CloudinaryImageProvider::fromConfig($this->_getConfigHelper()->buildConfig())
                ->upload($this->_getImage($result['path'] . DS . $result['file']));
            $this->_trackSync($result['path'] . DS . $result['file']);
        }
        return $this;
    }

    protected function _trackSync($fileName)
    {
        Mage::getModel('made_cloudinary/cms_sync')->setValue($fileName)->tagAsSynced();
    }
}