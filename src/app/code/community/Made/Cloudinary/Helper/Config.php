<?php

use CloudinaryExtension\Config;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dpr;
use CloudinaryExtension\Image\Transformation\FetchFormat;
use CloudinaryExtension\Image\Transformation\Gravity;
use CloudinaryExtension\Image\Transformation\Quality;
use CloudinaryExtension\Security\CloudinaryEnvVar;

class Made_Cloudinary_Helper_Config extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_ENABLED               = 'cloudinary/cloud/enabled';
    const CONFIG_PATH_ENVIRONMENT_VARIABLE  = 'cloudinary/setup/env_var';
    const CONFIG_DEFAULT_GRAVITY            = 'cloudinary/transforms/gravity';
    const CONFIG_DEFAULT_QUALITY            = 'cloudinary/transforms/image_quality';
    const CONFIG_DEFAULT_DPR                = 'cloudinary/transforms/image_dpr';
    const CONFIG_DEFAULT_FETCH_FORMAT       = 'cloudinary/transforms/fetch_format';
    const CONFIG_CDN_SUBDOMAIN              = 'cloudinary/config/cdn_subdomain';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const USER_PLATFORM_TEMPLATE = 'CloudinaryMagento/%s (Magento %s)';

    public function buildCredentials()
    {
        $envVar = CloudinaryEnvVar::fromString($this->getEnvVar());
        return $envVar->getCredentials();
    }

    public function getEnvVar()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::CONFIG_PATH_ENVIRONMENT_VARIABLE));
    }

    public function getDefaultGravity()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_GRAVITY);
    }

    public function getFetchFormat()
    {
        return Mage::getStoreConfig(self::CONFIG_DEFAULT_FETCH_FORMAT) === "1" ? FetchFormat::FETCH_FORMAT_AUTO : null;
    }

    public function getImageQuality()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_QUALITY);
    }

    public function getImageDpr()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DEFAULT_DPR);
    }

    public function getCdnSubdomainFlag()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_CDN_SUBDOMAIN);
    }

    public function isEnabled()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_PATH_ENABLED);
    }

    public function enable()
    {
        $this->_setStoreConfig(self::CONFIG_PATH_ENABLED, self::STATUS_ENABLED);
    }

    public function disable()
    {
        $this->_setStoreConfig(self::CONFIG_PATH_ENABLED, self::STATUS_DISABLED);
    }

    public function getUserPlatform()
    {
        return sprintf(
            self::USER_PLATFORM_TEMPLATE,
            Mage::getConfig()->getModuleConfig('Made_Cloudinary')->version,
            Mage::getVersion()
        );
    }

    public function buildConfig()
    {
        $config = Config::fromEnvVar(
            CloudinaryEnvVar::fromString($this->getEnvVar())
        );

        $config->setUserPlatform($this->getUserPlatform());

        if($this->getCdnSubdomainFlag()) {
            $config->enableCdnSubdomain();
        }

        $config->getDefaultTransform()
            ->withGravity(Gravity::fromString($this->getDefaultGravity()))
            ->withFetchFormat(FetchFormat::fromString($this->getFetchFormat()))
            ->withQuality(Quality::fromString($this->getImageQuality()))
            ->withDpr(Dpr::fromString($this->getImageDpr()))
        ;

        return $config;
    }

    protected function _setStoreConfig($configPath, $value)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($configPath, $value)->reinit();
    }

}
