<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;


class Made_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function uploadProductImage($imageDetails)
    {
        $this->_getImageProvider()->upload(
            $this->_getImage(Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath() . $imageDetails['file'])
        );

        Mage::getModel('made_cloudinary/sync')
            ->setValueId($imageDetails['value_id'])
            ->setValue($imageDetails['file'])
            ->tagAsSynced();
    }

    /**
     * @param $imageName
     * This expects a path and filename relative to the media storage root e.g. wysiwyg/homepage/best-seller.jpg
     */
    public function upload($image)
    {
        $this->_getImageProvider()->upload($this->_getImage($image));

        Mage::getModel('made_cloudinary/sync')
            ->setValue($imageName)
            ->tagAsSynced();
    }

    public function deleteImage($image)
    {
        $this->_getImageProvider()->deleteImage($this->_getImage($image));
    }

    public function getUrl($image)
    {
        return (string)$this->_getImageProvider()->transformImage($this->_getImage($image));
    }

    protected function _getImageProvider()
    {
        return CloudinaryImageProvider::fromConfig($this->_getConfigHelper()->buildConfig());
    }

}
