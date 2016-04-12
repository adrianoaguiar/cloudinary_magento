<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Made_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function getUrl()
    {
        if ($this->_serveFromCloud($this->_newFile)) {
            return (string)CloudinaryImageProvider::fromConfig($this->_getConfigHelper()->buildConfig())
                ->getTransformedImageUrl($this->_getImage($this->_newFile));
        }
        return parent::getUrl();
    }
}