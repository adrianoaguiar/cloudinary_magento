<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;


class Made_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    // THIS METHOD IS ONLY USED FOR CATALOG/PRODUCT IMAGES
    // THIS WHOLE CLASS BASICALLY PERTAINS TO PRODUCT IMAGES ONLY, WHICH IS A BIT STUPID REALLY
    public function upload(array $imageDetails)
    {
        $imageManager = $this->_getImageProvider();
        $imageManager->upload(Image::fromPath($this->_getMediaPath() . $this->_getImageDetailFromKey($imageDetails, 'file')));
        Mage::getModel('made_cloudinary/sync')
            ->setValueId($imageDetails['value_id'])
            ->setValue($imageDetails['file'])
            ->tagAsSynced();
    }

    public function deleteImage($imageName)
    {
        $this->_getImageProvider()->deleteImage(Image::fromPath($this->_getMediaPath() . $imageName));
    }

    public function getUrl($imagePath)
    {
        return (string)$this->_getImageProvider()->transformImage(Image::fromPath($this->_getMediaPath() . $imagePath));
    }

    /**
     * @return string
     * This is a bit silly because it makes the "base class" of cloudinary syncable objects as product images
     */
    protected function _getMediaPath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    protected function _getImageProvider()
    {
        return CloudinaryImageProvider::fromConfig($this->_getConfigHelper()->buildConfig());
    }

    protected function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if (!array_key_exists($key, $imageDetails)) {
            throw new Made_Cloudinary_Model_Exception_BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }
}
