<?php

namespace ImageProviders;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Security\EnvVar;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class FakeImageProvider implements ImageProvider {


    protected $key;
    protected $secret;
    protected $uploadedImageUrl = array();
    protected $credentials;
    protected $mockCloud;
    protected $cloud;

    public function __construct(EnvVar $envVar)
    {
        $this->credentials = $envVar->getCredentials();
        $this->cloud = $envVar->getCloud();
    }

    public function setMockCredentials(Key $aKey, Secret $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;
    }

    public function setMockCloud(Cloud $mockCloud)
    {
        $this->mockCloud = $mockCloud;
    }

    public function upload(Image $image)
    {
        $this->uploadedImageUrl[(string)$image] = 'uploaded image URL';
    }

    public function getImageUrlByName($image, $options = array())
    {
        $imageName = (string)$image;
        if($this->areCredentialsCorrect() && $this->isCloudCorrect()) {
            return array_key_exists($imageName, $this->uploadedImageUrl) ? $this->uploadedImageUrl[$imageName] : '';
        }
        return '';
    }

    public function validateCredentials()
    {
        return $this->areCredentialsCorrect();
    }

    protected function areCredentialsCorrect()
    {
        return (string)$this->credentials->getKey() === (string)$this->key && (string)$this->credentials->getSecret() === (string)$this->secret;
    }

    protected function isCloudCorrect()
    {
        return (string)$this->mockCloud == (string)$this->cloud;
    }

    public function transformImage(Image $image, \CloudinaryExtension\Image\Transformation $transformation)
    {
        $imageName = (string)$image;
        if($this->areCredentialsCorrect() && $this->isCloudCorrect()) {
            return array_key_exists($imageName, $this->uploadedImageUrl) ? $this->uploadedImageUrl[$imageName] : '';
        }
        return '';
    }

    public function deleteImage(Image $image)
    {
        unset($this->uploadedImageUrl[(string)$image]);
    }
}