<?php

use CloudinaryAdapter\Config;
use CloudinaryAdapter\Image\Transformation;
use CloudinaryAdapter\Image\Transformation\Dpr;
use CloudinaryAdapter\Image\Transformation\FetchFormat;
use CloudinaryAdapter\Image\Transformation\Gravity;
use CloudinaryAdapter\Image\Transformation\Quality;
use CloudinaryAdapter\Image\Transformation\Signature;
use CloudinaryAdapter\Security\CloudinaryEnvVar;

class Made_Cloudinary_Helper_Config extends Mage_Core_Helper_Abstract
{
    const CONFIG_ENABLED                = 'cloudinary/config/enabled';
    const CONFIG_ENVIRONMENT_VARIABLE   = 'cloudinary/config/environment_variable';
    const CONFIG_CDN_SUBDOMAIN          = 'cloudinary/config/cdn_subdomain';
    const CONFIG_GRAVITY                = 'cloudinary/transforms/gravity';
    const CONFIG_QUALITY                = 'cloudinary/transforms/quality';
    const CONFIG_DPR                    = 'cloudinary/transforms/dpr';
    const CONFIG_SIGNATURE              = 'cloudinary/transforms/signature';
    const CONFIG_FETCH_FORMAT           = 'cloudinary/transforms/fetch_format';
    const STATUS_ENABLED                = 1;
    const STATUS_DISABLED               = 0;
    const USER_PLATFORM_TEMPLATE        = 'CloudinaryMagento/%s (Magento %s)';

    public function buildCredentials()
    {
        $envVar = CloudinaryEnvVar::fromString($this->getEnvVar());
        return $envVar->getCredentials();
    }

    public function getEnvVar()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::CONFIG_ENVIRONMENT_VARIABLE));
    }

    public function getGravity()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_GRAVITY);
    }

    public function getFetchFormat()
    {
        return Mage::getStoreConfig(self::CONFIG_FETCH_FORMAT) === "1" ? FetchFormat::FETCH_FORMAT_AUTO : null;
    }

    public function getQuality()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_QUALITY);
    }

    public function getDpr()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_DPR);
    }

    public function getSignature()
    {
        return (string)Mage::getStoreConfig(self::CONFIG_SIGNATURE);
    }

    public function getCdnSubdomainFlag()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_CDN_SUBDOMAIN);
    }

    public function isEnabled()
    {
        return (boolean)Mage::getStoreConfig(self::CONFIG_ENABLED);
    }

    public function enable()
    {
        $this->_setStoreConfig(self::CONFIG_ENABLED, self::STATUS_ENABLED);
    }

    public function disable()
    {
        $this->_setStoreConfig(self::CONFIG_ENABLED, self::STATUS_DISABLED);
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
            ->withGravity(Gravity::fromString($this->getGravity()))
            ->withFetchFormat(FetchFormat::fromString($this->getFetchFormat()))
            ->withQuality(Quality::fromString($this->getQuality()))
            ->withDpr(Dpr::fromString($this->getDpr()))
            ->withSignature(Signature::fromString($this->getSignature()))
        ;

        return $config;
    }

    protected function _setStoreConfig($configPath, $value)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($configPath, $value)->reinit();
    }

}
