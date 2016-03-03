<?php

namespace ImageProviders;

use CloudinaryExtension\Config;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProvider;

class ConfigImageProvider implements ImageProvider
{

    protected $config;
    protected $subdomains = ['cdn1', 'cdn2'];
    protected $prefixCount = 0;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function upload(Image $image)
    {
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        $prefix =  $this->subdomains[$this->prefixCount % 2];

        if($this->config->getCdnSubdomainStatus() === true)
        {
            $this->prefixCount += 1;
        }

        return $prefix . "/" . $image;
    }

    public function deleteImage(Image $image)
    {
    }

    public function validateCredentials()
    {
    }
}