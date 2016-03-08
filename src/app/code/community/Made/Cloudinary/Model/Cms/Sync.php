<?php

use CloudinaryExtension\Image\Syncable;

class Made_Cloudinary_Model_Cms_Sync extends Mage_Core_Model_Abstract implements Syncable
{

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    public function getFilename()
    {
        return $this->getData('filename');
    }

    public function setValue($fileName)
    {
        $this->setData('basename', basename($fileName));

        return $this;
    }

    public function tagAsSynced()
    {
        $this->setData('image_name', $this->getData('basename'));
        $this->setData('media_gallery_id', null);
        $this->setData('id', null);

        $this->save();
    }

}