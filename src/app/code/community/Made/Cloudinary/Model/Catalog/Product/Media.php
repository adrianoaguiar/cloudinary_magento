<?php

class Made_Cloudinary_Model_Catalog_Product_Media extends Mage_Core_Model_Abstract
{

    protected $_newImages;

    public function newImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_setNewImages($product->getData('media_gallery'));
        return $this->_getNewImages($product);
    }

    protected function _setNewImages(array $mediaGallery)
    {
        $this->_newImages = array();

        foreach ($mediaGallery['images'] as $image) {
            if (array_key_exists('new_file', $image)) {
                $this->_newImages[] = $image['new_file'];
            }
        }
    }

    protected function _getNewImages(Mage_Catalog_Model_Product $product)
    {
        $product->load('media_gallery');
        $gallery = $product->getData('media_gallery');
        return array_filter($gallery['images'], array($this, '_isImageInArray'));
    }

    protected function _isImageInArray($toFilter)
    {
        return is_array($toFilter) && array_key_exists('file', $toFilter) && in_array($toFilter['file'], $this->_newImages);
    }

    public function removedImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        return $this->_getRemovedImages($product->getMediaGallery());
    }

    protected function _getRemovedImages(array $mediaGallery)
    {
        if (!is_array($mediaGallery['images'])) {
            $mediaGallery['images'] = json_decode($mediaGallery['images'], true);
        }

        return array_filter($mediaGallery['images'], array($this, '_isImageRemoved'));
    }

    protected function _isImageRemoved($toFilter)
    {
        return is_array($toFilter) && array_key_exists('removed', $toFilter) && $toFilter['removed'] === 1;
    }
}