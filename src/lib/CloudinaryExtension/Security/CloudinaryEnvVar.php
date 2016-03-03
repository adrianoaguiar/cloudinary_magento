<?php

namespace CloudinaryExtension\Security;

use Cloudinary;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;

class CloudinaryEnvVar implements EnvVar
{

    protected $environmentVariabe;

    protected function __construct($envVar)
    {
        $this->envVar = (string)$envVar;
        Cloudinary::config_from_url(str_replace('CLOUDINARY_URL=', '', $envVar));
    }

    public static function fromString($envVar)
    {
        return new CloudinaryEnvVar($envVar);
    }

    public function getCloud()
    {
        return Cloud::fromName(Cloudinary::config_get('cloud_name'));
    }

    public function getCredentials()
    {
        return new Credentials(
            Key::fromString(Cloudinary::config_get('api_key')),
            Secret::fromString(Cloudinary::config_get('api_secret'))
        );
    }

    public function __toString()
    {
        return $this->envVar;
    }

}