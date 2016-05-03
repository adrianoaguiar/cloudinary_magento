<?php

use CloudinaryAdapter\Image\Syncable;

class Made_Cloudinary_Model_Sync extends Mage_Core_Model_Abstract implements Syncable
{

    // we could use a trait here for the remove media path stuff

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    public function tagAsSynced()
    {
        $this->setData('media_path', $this->getRelativePath());
        $this->setData('media_gallery_id', $this->getData('value_id'));  // Made_Cloudinary_Model_Image::upload
        $this->unsetData('value_id');
        $this->save();
    }

    public function isImageInCloudinary($imageName)
    {
        $this->load($imageName, 'media_path');
        return !is_null($this->getId());
    }

    public function getFilename()
    {
        if (!$this->getValue()) {
            return null;
        }
        return Mage::getModel('catalog/product_media_config')->getBaseMediaPath() . $this->getValue();
    }

    public function getRelativePath()
    {
        if (!$this->getValue()) {
            return null;
        }
        return Mage::getModel('catalog/product_media_config')->getBaseMediaUrlAddition() . $this->getValue();
    }
}