<?php

namespace CloudinaryExtension;

class Image
{
    protected $imagePath;
    protected $pathParts;
    protected $relativeImagePath;

    protected function __construct($imagePath, $pathPrefix = null)
    {
        $this->relativeImagePath = $this->imagePath = $imagePath;
        if($pathPrefix) {
            $this->relativeImagePath = $this->removePathPrefix($imagePath, $pathPrefix);
        }
        $this->pathParts = pathinfo($this->relativeImagePath);
    }

    protected function removePathPrefix($imagePath, $pathPrefix) {

        if(substr($pathPrefix, -1) != DIRECTORY_SEPARATOR) {
            $pathPrefix = $pathPrefix . DIRECTORY_SEPARATOR;
        }

        if(0 === strpos($imagePath, $pathPrefix)) {
            return substr($imagePath, strlen($pathPrefix));
        }
        return $imagePath;
    }

    public static function fromPath($imagePath, $pathPrefix = null)
    {
        if(is_null($pathPrefix)) {
            Mage::log(__METHOD__ . ': 2nd arg $pathPrefix must not be null (you are probably seeing a broken image)');
        }
        return new Image($imagePath, $pathPrefix);
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

    public function getRelativePath()
    {
        return $this->relativeImagePath;
    }


}
