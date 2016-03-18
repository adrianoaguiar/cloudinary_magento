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
        $this->relativeImagePath = $this->removeMediaPrefix($imagePath);
        $this->pathParts = pathinfo($this->relativeImagePath);
    }

    // OK this method, unlike the rest of the lib/extension, is still a bit Magento-specific.  GRANT TODO improve me
    protected function removeMediaPrefix($imagePath) {
        if(0 === strpos($imagePath, \Mage::getBaseDir('media') . DS)) {
            return substr($imagePath, strlen(\Mage::getBaseDir('media') . DS));
        }
        return $imagePath;
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
        return $this->pathParts['dirname'] . '/' . $this->pathParts['filename'];
    }

    public function getExtension()
    {
        return $this->pathParts['extension'];
    }


}
