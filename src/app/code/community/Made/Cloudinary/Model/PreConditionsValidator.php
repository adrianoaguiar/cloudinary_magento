<?php

use CloudinaryAdapter\Image;

trait  Made_Cloudinary_Model_PreConditionsValidator
{
    protected function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    protected function _isImageInCloud($imageName)
    {
        return Mage::getModel('made_cloudinary/sync')->isImageInCloudinary($imageName);
    }

    protected function _getConfigHelper()
    {
        return Mage::helper('made_cloudinary/config');
    }

    protected function _serveFromCloud($file)
    {
        return $this->_isEnabled() && $this->_isImageInCloud($file);
    }

    protected function _getImage($imagePath)
    {
        return Image::fromPath($imagePath, Mage::getBaseDir('media'));
    }
}
 