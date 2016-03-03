<?php

class Made_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{

    const CLOUDINARY_CONFIG_SECTION = 'cloudinary';

    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        Mage::helper('made_cloudinary/autoloader')->register();

        return $event;
    }

    public function uploadImagesToCloudinary(Varien_Event_Observer $event)
    {
        if (Mage::helper('made_cloudinary/config')->isEnabled()) {
            $cloudinaryImage = Mage::getModel('made_cloudinary/image');

            foreach ($this->_getImagesToUpload($event->getProduct()) as $image) {
                $cloudinaryImage->upload($image);
            }
        }
    }

    public function validateCloudinaryCredentials(Varien_Event_Observer $observer)
    {
        $configObject = $observer->getEvent()->getObject();
        if ($this->_isNotCloudinaryConfigSection($configObject)) {
            return;
        }

        try {
            $this->_validateEnvVarFromConfigObject($configObject);
        } catch (Exception $e) {
            $this->_addErrorMessageToAdminSession($e);
            $this->_logException($e);
        }
    }

    protected function _getImagesToUpload(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('made_cloudinary/catalog_product_media')->newImagesForProduct($product);
    }

    public function deleteImagesFromCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('made_cloudinary/image');

        foreach ($this->_getImagesToDelete($event->getProduct()) as $image) {
            $cloudinaryImage->deleteImage($image['file']);
        }
    }

    protected function _getImagesToDelete(Mage_Catalog_Model_Product $product)
    {
        $productMedia = Mage::getModel('made_cloudinary/catalog_product_media');
        return $productMedia->removedImagesForProduct($product);
    }

    protected function _flattenConfigData(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        $configData = array();
        $groups = $configObject->getGroups();

        if ($this->_containsSetup($groups)) {
            $configData = array_map(
                function($field) {
                    return $field['value'];
                },
                $groups['setup']['fields']
            );
        }
        return $configData;
    }

    protected function _isNotCloudinaryConfigSection(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        return $configObject->getSection() != self::CLOUDINARY_CONFIG_SECTION;
    }

    protected function _validateEnvVarFromConfigObject(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        $configData = $this->_flattenConfigData($configObject);
        $cloudinaryConfig = Mage::helper('made_cloudinary/config_validation');

        $cloudinaryConfig->validateEnvVar(
            $configData['cloudinary_environment_variable']
        );
    }

    protected function _addErrorMessageToAdminSession($e)
    {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    }

    protected function _logException($e)
    {
        Mage::logException($e);
    }

    protected function _containsSetup($groups)
    {
        return array_key_exists('setup', $groups);
    }
}