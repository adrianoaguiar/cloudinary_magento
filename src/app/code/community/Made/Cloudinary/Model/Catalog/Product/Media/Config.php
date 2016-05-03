<?php

use CloudinaryAdapter\Cloud;
use CloudinaryAdapter\CloudinaryImageProvider;
use CloudinaryAdapter\Image;

class Made_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function getMediaUrl($file)
    {
        $cloudinaryFile = $this->getBaseMediaUrlAddition() . $file;
        if ($this->_serveFromCloud($cloudinaryFile)) {
            return $this->_getUrlForImage($cloudinaryFile);
        }

        return parent::getMediaUrl($file);
    }

    public function getTmpMediaUrl($file)
    {
        $cloudinaryFile = $this->getBaseMediaUrlAddition() . $file;
        if ($this->_serveFromCloud($cloudinaryFile)) {
            return $this->_getUrlForImage($cloudinaryFile);
        }

        return parent::getTmpMediaUrl($file);
    }

    protected function _getUrlForImage($file)
    {
        return (string)CloudinaryImageProvider::fromConfig($this->_getConfigHelper()->buildConfig())
            ->getTransformedImageUrl($this->_getImage($file));
    }
}
