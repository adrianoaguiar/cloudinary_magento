<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\EnvVar;

class Config
{
    protected $credentials;

    protected $cloud;

    protected $defaultTransform;

    protected $cdnSubdomain = true;

    protected $userPlatform = '';

    protected function __construct(Cloud $cloud,Credentials $credentials)
    {
        $this->cdnSubdomain = false;
        $this->credentials = $credentials;
        $this->cloud = $cloud;
        $this->defaultTransform = Transformation::builder();
    }

    public static function fromCloudAndCredentials(Cloud $cloud, Credentials $credentials)
    {
        return new Config($cloud, $credentials);
    }

    public static function fromEnvVar(EnvVar $envVar)
    {
        return new Config($envVar->getCloud(), $envVar->getCredentials());
    }

    public function getCloud()
    {
        return $this->cloud;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function getDefaultTransform()
    {
        return $this->defaultTransform;
    }

    public function build()
    {
        $config = $this->getMandatoryConfig();
        if($this->cdnSubdomain) {
            $config['cdn_subdomain'] = true;
        }
        return $config;
    }

    public function enableCdnSubdomain()
    {
        $this->cdnSubdomain = true;
    }

    public function getCdnSubdomainStatus()
    {
        return $this->cdnSubdomain;
    }

    protected function getMandatoryConfig()
    {
        return array(
            "cloud_name" => (string)$this->cloud,
            "api_key" => (string)$this->credentials->getKey(),
            "api_secret" => (string)$this->credentials->getSecret()
        );
    }

    /**
     * @return string
     */
    public function getUserPlatform()
    {
        return $this->userPlatform;
    }

    /**
     * @param string $userPlatform
     */
    public function setUserPlatform($userPlatform)
    {
        $this->userPlatform = $userPlatform;
    }
}
