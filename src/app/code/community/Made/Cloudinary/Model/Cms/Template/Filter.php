<?php

use CloudinaryAdapter\Image;
use CloudinaryAdapter\Image\Transformation\Dimensions;

class Made_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        $p = $this->_getIncludeParameters($construction[2]);

        $dimensions = null;
        if(!empty($p['width'])) {
            $dimensions = Dimensions::fromWidthAndHeight($p['width'], !empty($p['height']) ? $p['height'] : $p['width']);
        }

        if ($this->_serveFromCloud($p['url'])) {
            return Mage::getModel('made_cloudinary/image')->getUrl($p['url'], $dimensions);
        }

        return parent::mediaDirective($construction);
    }
}