<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Config;
use CloudinaryExtension\Exception\InvalidCredentials;
use CloudinaryExtension\Security\CloudinaryEnvVar;

class Made_Cloudinary_Helper_Config_Validation extends Mage_Core_Helper_Abstract
{

    public function validateEnvVar($envVar)
    {
        $config = $this->_getConfig($envVar);
        $imageProvider = CloudinaryImageProvider::fromConfig($config);

        if (!$imageProvider->validateCredentials()) {
            throw new InvalidCredentials("There was a problem validating your Cloudinary credentials.");
        }
    }

    protected function _getConfig($envVar)
    {
        return Config::fromEnvVar(
            CloudinaryEnvVar::fromString($envVar)
        );
    }

}