<?php

use CloudinaryExtension\Image\Syncable;

class Made_Cloudinary_Model_Sync extends Mage_Core_Model_Abstract implements Syncable
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    public function tagAsSynced()
    {
        $this->setData('image_name', $this['value']);
//        $this->setData('image_name', basename($this['value']));
        $this->setData('media_gallery_id', $this['value_id']);
        $this->unsetData('value_id');

        $this->save();
    }

    public function isImageInCloudinary($imageName)
    {
        $this->load($imageName, 'image_name');
        return !is_null($this->getId());
    }

    public function getFilename()
    {
        if (!$this->getValue()) {
            return null;
        }
        return Mage::getBaseDir('media') . $this->getValue();
        //return $this->_baseMediaPath() . $this->getValue();
    }

    // NOT USED ANY MORE
    protected function _baseMediaPath()
    {
        return Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
    }
}