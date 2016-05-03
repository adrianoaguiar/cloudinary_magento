<?php

use CloudinaryAdapter\Image;

class Made_Cloudinary_Model_Cms_Adminhtml_Template_Filter extends Mage_Cms_Model_Adminhtml_Template_Filter
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        $directiveParams = $construction[2];
        $params = $this->_getIncludeParameters($directiveParams);

        if (!isset($params['url'])) {
            Mage::throwException('Undefined url parameter for media directive.');
        }

        $allowRemoteFileOpen = ini_get('allow_url_fopen');

        if ($this->_isEnabled() && $allowRemoteFileOpen) {

            $imagePath = $params['url'];

            if ($this->_serveFromCloud($imagePath)) {
                return Mage::getModel('made_cloudinary/image')->getUrl($imagePath);
            }
        }

        return parent::mediaDirective($construction);
    }
}