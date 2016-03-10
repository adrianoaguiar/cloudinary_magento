<?php

use CloudinaryExtension\Image\Syncable;

class Made_Cloudinary_Model_Cms_Sync extends Mage_Core_Model_Abstract implements Syncable
{
    // we could use a trait here for the remove media path stuff

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    // only needed because this implements Syncable
    public function getFilename()
    {
        return $this->getData('filename');
    }

    public function setValue($fileName)
    {
        Mage::log(__METHOD__ . ' ' . $fileName . ' GRANT XXXX THIS IS USEFUL OTHERWISE KILL ME NOW');
        //$this->setData('basename', basename($fileName));
        $this->setData('pathname', $this->removeMediaPrefix($fileName));
        return $this;
    }

    public function tagAsSynced()
    {
//        $this->setData('image_name', $this->getData('pathname') ?: $this->removeMediaPrefix($this->getData('filename')));
//        $this->setData('image_name', $this->getData('basename'));
        $this->setData('media_gallery_id', null);
        $this->setData('id', null);
        $this->save();
    }


    public function addData(array $arr)
    {
        parent::addData($arr);
        $this->setData('image_name', $this->getData('pathname') ?: $this->removeMediaPrefix($this->getData('filename')));

        Mage::log($this->getData('image_name'));

        return $this;
    }

    protected function removeMediaPrefix($imagePath) {
        if(0 === strpos($imagePath, \Mage::getBaseDir('media') . DS)) {
            return substr($imagePath, strlen(\Mage::getBaseDir('media') . DS));
        }
        return $imagePath;
    }


}