<?php

use CloudinaryExtension\Image;

class Made_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        if ($this->_isEnabled()) {
            $imagePath = $this->_getImagePath($construction[2]);

            if ($this->_imageShouldComeFromCloudinary($imagePath)) {
                return Mage::getModel('made_cloudinary/image')->getUrl($imagePath);
            }
        }
        return parent::mediaDirective($construction);
    }

    private function _getImagePath($directiveParams)
    {
        $params = $this->_getIncludeParameters($directiveParams);
        return $params['url'];
    }

}