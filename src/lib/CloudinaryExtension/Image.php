<?php

namespace CloudinaryExtension;

class Image
{
    protected $imagePath;

    protected $pathParts;

    protected function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
        $this->pathParts = pathinfo(basename($this->imagePath));
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
        return $this->pathParts['filename'];
    }

    public function getExtension()
    {
        return $this->pathParts['extension'];
    }
}
