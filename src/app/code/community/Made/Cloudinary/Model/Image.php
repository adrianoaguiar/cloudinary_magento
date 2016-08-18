<?php

use CloudinaryAdapter\CloudinaryImageProvider;
use CloudinaryAdapter\Image;


class Made_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    protected $_config;

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
     * @param $imagePath path and filename relative to the media storage root e.g. catalog/product/p/r/product.jpg
     */
    public function upload($imagePath)
    {
        $this->_getImageProvider()->upload($this->_getImage($imagePath));

        Mage::getModel('made_cloudinary/sync')
            ->setValue($imagePath)
            ->tagAsSynced();
    }

    /**
     * @param $imagePath path and filename relative to the media storage root e.g. wysiwyg/homepage/best-seller.jpg
     */
    public function deleteImage($imagePath)
    {
        $this->_getImageProvider()->deleteImage($this->_getImage($imagePath));
    }

    /**
     * @param $imagePath path and filename relative to the media storage root e.g. catalog/product/p/r/product.jpg
     * @param $transform optional instance of CloudinaryAdapter\Image\Transformation
     */
    public function getUrl($imagePath, Image\Transformation $transform = null)
    {
        return (string)$this->_getImageProvider()->getTransformedImageUrl($this->_getImage($imagePath), $transform);
    }

    protected function _getImageProvider()
    {
        return CloudinaryImageProvider::fromConfig($this->_getConfig());
    }

    protected function _getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = $this->_getConfigHelper()->buildConfig();
        }
        return $this->_config;
    }

}
