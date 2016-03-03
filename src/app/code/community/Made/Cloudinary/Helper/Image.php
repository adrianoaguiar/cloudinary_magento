<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation\Dimensions;

class Made_Cloudinary_Helper_Image extends Mage_Catalog_Helper_Image
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    protected $_imageProvider;
    protected $_dimensions;
    protected $_attributeName;
    protected $_config;

    // Made - new fields to reduce query count
    protected $_isEnabled;
    protected $_syncCollection;
    protected $_syncResults;

    public function __construct()
    {
        $this->_isEnabled = $this->_getConfigHelper()->isEnabled();
        $this->_config = $this->_getConfigHelper()->buildConfig();
        $this->_imageProvider = CloudinaryImageProvider::fromConfig(
            $this->_config
        );
        // for speed, we are looking up images in Cloudinary sync table with SQL, it can happen many times on a single page
        // remember helpers are singletons so this only ever happens once
        $this->_readHandle = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_syncTable = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/sync');
    }

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ($this->_isEnabled) {
            $this->_dimensions = Dimensions::null();
            $this->_attributeName = $attributeName;
        }
        return parent::init($product, $attributeName, $imageFile);
    }

    public function resize($width, $height = null)
    {
        if ($this->_imageShouldComeFromCloudinary($this->_getRequestedImageFile())) {
            $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height ?: $width);
            return $this;
        }
        return parent::resize($width, $height);
    }

    protected function _getRequestedImageFile()
    {
        return $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
    }

    public function __toString()
    {
        $imageFile = $this->_getRequestedImageFile();

        if ($this->_imageShouldComeFromCloudinary($imageFile)) {
            $image = Image::fromPath($imageFile);
            $transformation = $this->_config->getDefaultTransformation()->withDimensions($this->_dimensions);
            return (string)$this->_imageProvider->transformImage($image, $transformation);
        }
        return parent::__toString();
    }

    /**
     * Set a lookup array of image names from product collection IDS
     * This is so that we can avoid going to the database for every product
     */
    public function setProductCollection(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $resource = Mage::getSingleton('core/resource');

        $galleryTable = $resource->getTableName('made_cloudinary/catalog_media_gallery');
        $syncTable = $resource->getTableName('made_cloudinary/sync');
        $read = $resource->getConnection('core_read');
        $select = $read->select();

        $select->from(['a' => $syncTable], 'image_name')
            ->join(['b' => $galleryTable], 'b.value_id = a.media_gallery_id', 'entity_id')
            ->where('a.cloudinary_sync_id is not null')
            ->where('b.entity_id IN (?)', $collection->getAllIds());

        $this->_syncCollection = $read->fetchPairs($select);
    }

    /**
     * Overridding trait behaviour here as image helpers are called often
     */
    protected function _imageShouldComeFromCloudinary($file)
    {
        return $this->_isEnabled && $this->_isImageInCloudinary(basename($file));
    }

    protected function _isImageInCloudinary($imageName)
    {
        // have we already looked up the images from the sync table using a product collection?
        if(!is_null($this->_syncCollection)) {
            return isset($this->_syncCollection[$imageName]);
        }

        // if we've already had a result for this product image name, use it instead of looking it up again
        // the trait looks this up via model, but this is slow if called multiple times, so using db handle (below)
        if(!isset($this->_syncResults[$imageName])) {
            $this->_syncResults[$imageName] = $this->_findImageInSyncTable($imageName);
        }
        return $this->_syncResults[$imageName];
    }

    protected function _findImageInSyncTable($imageName)
    {
        return (bool) $this->_readHandle->fetchOne(
            'SELECT cloudinary_sync_id from ' . $this->_syncTable . ' WHERE image_name = ?', [$imageName]
        );
    }

}
