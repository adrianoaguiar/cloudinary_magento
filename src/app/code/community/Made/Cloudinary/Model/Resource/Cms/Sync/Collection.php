<?php

use CloudinaryExtension\Export\SyncedMediaRepo;

class Made_Cloudinary_Model_Resource_Cms_Sync_Collection
    extends Mage_Cms_Model_Wysiwyg_Images_Storage_Collection
    implements SyncedMediaRepo
{
    /**
     * @var string[]
     * @link http://cloudinary.com/documentation/image_transformations#format_conversion
     * @link http://cloudinary.com/documentation/upload_images
     */
    protected $allowedImgExtensions = ['JPG', 'PNG', 'GIF', 'BMP', 'TIFF', 'EPS', 'PSD', 'SVG', 'WebP'];

    public function __construct()
    {
        $this->addTargetDir(Mage::helper('cms/wysiwyg_images')->getStorageRoot());
        $this->setItemObjectClass('made_cloudinary/cms_sync');
        $this->setFilesFilter(
            sprintf('#^[a-z0-9\.\-\_]+\.(?:%s)$#i', implode('|', $this->allowedImgExtensions))
        );
    }

    public function addTargetDir($value)
    {
        try {
            parent::addTargetDir($value);
        } catch (Exception $e) {
            Mage::logException($e);
            if (!Mage::registry('error_' . $value)) {
                Mage::getSingleton('core/session')->addError("Couldn't find path " . $value);
                Mage::register('error_' . $value, true);
            }
            throw $e;
        }
    }

    public function findUnsyncedImages()
    {
        $this->addFieldToFilter('basename', array('nin' => $this->_getSyncedImageNames()));

        return $this->getItems();
    }

    protected function _getSyncedImageNames()
    {
        return array_map(
            function ($itemData) {
                return $itemData['image_name'];
            },
            $this->_getSyncedImageData()
        );
    }

    protected function _getSyncedImageData()
    {
        return Mage::getResourceModel('made_cloudinary/sync_collection')
            ->addFieldToSelect('image_name')
            ->addFieldToFilter('media_gallery_id', array('null' => true))
            ->distinct(true)
            ->getData();
    }

}
