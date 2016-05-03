<?php

namespace ImageProviders;

use CloudinaryAdapter\Config;
use CloudinaryAdapter\Credentials;
use CloudinaryAdapter\Image;
use CloudinaryAdapter\Image\Transformation;
use CloudinaryAdapter\ImageProvider;

class TransformingImageProvider implements ImageProvider
{

    protected $images = array();

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function upload(Image $image)
    {
        $this->images[(string)$image] = $image;
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        return http_build_query($transformation->build()) .'/'. $this->images[(string)$image];
    }

    public function deleteImage(Image $image)
    {
    }

    public function validateCredentials()
    {
    }

}