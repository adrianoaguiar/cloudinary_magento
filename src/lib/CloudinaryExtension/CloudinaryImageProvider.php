<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security;

class CloudinaryImageProvider implements ImageProvider
{
    protected $config;

    protected function __construct(Config $config)
    {
        $this->config = $config;
        $this->authorise();
    }

    public static function fromConfig(Config $config)
    {
        return new CloudinaryImageProvider($config);
    }

    public function upload(Image $image)
    {
        Uploader::upload((string)$image, array("public_id" => $image->getId()));
    }

    public function getTransformedImageUrl(Image $image, Transformation $transformation = null)
    {
        if ($transformation === null) {
            $transformation = $this->config->getDefaultTransform();
        }
        return \cloudinary_url($image->getId(), $transformation->build());
    }

    public function validateCredentials()
    {
        $signedValidationUrl = $this->getSignedValidationUrl();
        return $this->validationResult($signedValidationUrl);
    }

    public function deleteImage(Image $image)
    {
        Uploader::destroy($image->getId());
    }

    protected function authorise()
    {
        Cloudinary::config($this->config->build());
        Cloudinary::$USER_PLATFORM = $this->config->getUserPlatform();
    }

    protected function getSignedValidationUrl()
    {
        $consoleUrl = Security\ConsoleUrl::fromPath("media_library/cms");
        return (string)Security\SignedConsoleUrl::fromConsoleUrlAndCredentials(
            $consoleUrl,
            $this->config->getCredentials()
        );
    }

    protected function validationResult($signedValidationUrl)
    {
        $request = new ValidateRemoteUrlRequest($signedValidationUrl);
        return $request->validate();
    }
}
