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

    // Made - new fields to reduce query count and otherwise optimise execution
    protected $_isEnabled;
    protected $_syncCollection;
    protected $_syncResults;
    protected $_catalogProductPath;
    protected $_readHandle;
    protected $_syncTable;
    protected $_mediaPath;

    protected $_image;

    public function __construct()
    {
        $this->_isEnabled = $this->_getConfigHelper()->isEnabled();
        $this->_config = $this->_getConfigHelper()->buildConfig();
        $this->_imageProvider = CloudinaryImageProvider::fromConfig(
            $this->_config
        );
        // for speed, we may look up images in Cloudinary sync table with SQL, it can happen many times on a single page
        // remember helpers are singletons so this only ever happens once
        $this->_readHandle = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_syncTable = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/sync');

        $this->_catalogProductPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
        $this->_mediaPath = Mage::getBaseDir('media');
    }

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        parent::init($product, $attributeName, $imageFile);
        if ($this->_isEnabled) {
            $this->_dimensions = Dimensions::null();
            $this->_attributeName = $attributeName;
            $this->_image = Image::fromPath($this->_getRequestedFile(), $this->_mediaPath);
        }
        return $this;
    }

    public function resize($width, $height = null)
    {
        if ($this->_serveFromCloud($this->_image->getRelativePath())) {
            $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height ?: $width);
            return $this;
        }
        return parent::resize($width, $height);
    }

    /**
     * @return string complete path to image file on local media filesystem
     */
    protected function _getRequestedFile()
    {
        // $file will be the image file stored with regular product semantics e.g. /c/a/catalog_image.jpg
        $file = $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
        return $this->_catalogProductPath . $file;
    }

    public function __toString()
    {
        if ($this->_serveFromCloud($this->_image->getRelativePath())) {
            return (string)$this->_imageProvider->getTransformedImageUrl(
                $this->_image,
                $this->_config->getDefaultTransform()->withDimensions($this->_dimensions)
            );
        }
        return parent::__toString();
    }

    /**
     * Set a lookup array of image names from product collection IDS
     * This is so that we can avoid going to the database for every product
     */
    public function setProductCollection(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $galleryTable = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/catalog_media_gallery');
        $select = $this->_readHandle->select();

        $select->from(['a' => $this->_syncTable], 'media_path')
            ->join(['b' => $galleryTable], 'b.value_id = a.media_gallery_id', 'entity_id')
            ->where('a.id is not null')
            ->where('b.entity_id IN (?)', $collection->getAllIds());

        $this->_syncCollection = $this->_readHandle->fetchPairs($select);
    }

    /** 
     * Set a lookup array of image names from product gallery IDS 
     * This is so that we can avoid going to the database per image 
     */
    public function setMediaGalleryCollection(Varien_Data_Collection $collection)
    {
        $galleryTable = Mage::getSingleton('core/resource')->getTableName('made_cloudinary/catalog_media_gallery');
        $select = $this->_readHandle->select();

        $select->from(['a' => $this->_syncTable], 'media_path')
			->join(['b' => $galleryTable], 'b.value_id = a.media_gallery_id', 'entity_id')
            ->where('a.id is not null')
            ->where('b.value_id IN (?)', $collection->getAllIds());

        $this->_syncCollection = $this->_readHandle->fetchPairs($select);
	}

    /**
     * Overriding trait behaviour here as image helpers are called *often*, and these implementations improve speed
     */
    protected function _serveFromCloud($file)
    {
        return $this->_isEnabled and $this->_isImageInCloud($file); // NB short-circuit and operator
    }

    /*
     * @param $imagePath string image path in sync table e.g. catalog/product/c/a/catalog_image.jpg
     * @return bool
     */
    protected function _isImageInCloud($imagePath)
    {
        // have we already looked up the images from the sync table using a product collection?
        if(!is_null($this->_syncCollection)) {
            return isset($this->_syncCollection[$imagePath]);
        }

        // if we've already had a result for this product image name, use it instead of looking it up again
        // the trait looks this up via model, but this is slow if called multiple times, so using db handle (below)
        if(!isset($this->_syncResults[$imagePath])) {
            $this->_syncResults[$imagePath] = $this->_findInSyncTable($imagePath);
        }
        return $this->_syncResults[$imagePath];
    }

    /**
     * @param $imagePath string image path in sync table e.g. catalog/product/c/a/catalog_image.jpg
     * @return bool
     */
    protected function _findInSyncTable($imagePath)
    {
        return (bool) $this->_readHandle->fetchOne(
            'SELECT id from ' . $this->_syncTable . ' WHERE media_path = ?', [$imagePath]
        );
    }

}
