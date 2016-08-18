<?php

use CloudinaryAdapter\Image;
use CloudinaryAdapter\Image\Transformation;

class Made_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Made_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        $p = $this->_getIncludeParameters($construction[2]);

        if ($this->_serveFromCloud($p['url'])) {
            return Mage::getModel('made_cloudinary/image')->getUrl($p['url']);
        }

        return parent::mediaDirective($construction);
    }

    /**
     * This method name is not perhaps ideal, but the template filter imposes a length limit on directive names
     * @param $construction
     * @return string
     */
    public function cloudmediaDirective($construction)
    {
        $p = $this->_getIncludeParameters($construction[2]);

        if (!$this->_serveFromCloud($p['url'])) {
            return parent::mediaDirective($construction);
        }

        $transform = null;

        if(count($p > 1)) { // we have more than just a URL component
            $transform = Mage::helper('made_cloudinary/config')->buildConfig()->getDefaultTransform();

            if(!empty($p['width'])) {
                $transform->withDimensions(
                    Transformation\Dimensions::fromWidthAndHeight($p['width'], !empty($p['height']) ? $p['height'] : $p['width'])
                );
            }

            if(!empty($p['gravity'])) {
                $transform->withGravity(Transformation\Gravity::fromString($p['gravity']));
            }

            if(!empty($p['crop'])) {
                $transform->withCrop(Transformation\Crop::fromString($p['crop']));
            }

            if(!empty($p['aspectratio'])) {
                $transform->withAspectRatio(Transformation\AspectRatio::fromString($p['aspectratio']));
            }
        }

        return Mage::getModel('made_cloudinary/image')->getUrl($p['url'], $transform);

    }

}