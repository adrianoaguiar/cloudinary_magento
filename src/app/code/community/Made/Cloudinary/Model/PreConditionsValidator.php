<?php
trait  Made_Cloudinary_Model_PreConditionsValidator
{
    private function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    private function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('made_cloudinary/synchronisation')->isImageInCloudinary($imageName);
    }

    private function _getConfigHelper()
    {
        return Mage::helper('made_cloudinary/configuration');
    }

    private function _imageShouldComeFromCloudinary($file)
    {
        return $this->_isEnabled() && $this->_isImageInCloudinary(basename($file));
    }
}
 