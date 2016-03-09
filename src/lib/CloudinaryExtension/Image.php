<?php

namespace CloudinaryExtension;

class Image
{
    protected $imagePath;
    protected $pathParts;
    protected $relativeImagePath;

    protected function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
        //$this->pathParts = pathinfo(basename($this->imagePath));
        $this->pathParts = pathinfo($this->imagePath);
        $this->relativeImagePath = $this->removeMediaPrefix($imagePath);
    }

    protected function removeMediaPrefix($imagePath) {
        if(0 === strpos($imagePath, Mage::getBaseDir('media'))) {
            return substr($imagePath, strlen(Mage::getBaseDir('media')));
        }
        throw new Made_Cloudinary_Model_Exception_BadFilePathException("Recieved imagePath without Magento Media Prefix:" . $imagePath);
    }

    public static function fromPath($anImagePath)
    {
        return new Image($anImagePath);
    }

    public function __toString()
    {
        return $this->imagePath;
    }

    public function getId()
    {
        //return $this->pathParts['filename'];
        return $this->relativeImagePath;
    }

    public function getExtension()
    {
        return $this->pathParts['extension'];
    }


}
