<?php
trait  Made_Cloudinary_Model_PreConditionsValidator
{
    protected function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    protected function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('made_cloudinary/synchronisation')->isImageInCloudinary($imageName);
    }

    protected function _getConfigHelper()
    {
        return Mage::helper('made_cloudinary/configuration');
    }

    protected function _imageShouldComeFromCloudinary($file)
    {
        return $this->_isEnabled() && $this->_isImageInCloudinary(basename($file));
    }
}
 