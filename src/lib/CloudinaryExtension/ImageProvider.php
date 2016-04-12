<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Transformation;

interface ImageProvider
{
    public function upload(Image $image);
    public function getTransformedImageUrl(Image $image, Transformation $transformation);
    public function deleteImage(Image $image);
    public function validateCredentials();
}
