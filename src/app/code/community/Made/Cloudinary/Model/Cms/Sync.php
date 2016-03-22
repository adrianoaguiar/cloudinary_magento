<?php

use CloudinaryExtension\Image\Syncable;

class Made_Cloudinary_Model_Cms_Sync extends Mage_Core_Model_Abstract implements Syncable
{
    // we could use a trait here for the remove media path stuff

    protected function _construct()
    {
        $this->_init('made_cloudinary/sync');
    }

    public function setValue($fileName)
    {
        $this->setData('media_path', $this->removeMediaPrefix($fileName));
        return $this;
    }

    public function tagAsSynced()
    {
        if(!$this->getData('media_path')) {
            Mage::log(__METHOD__ . ': did not have media path when trying doing tagAsSync for ' . $this->getData('filename'));
            $this->setData('media_path', $this->removeMediaPrefix($this->getData('filename')));
        }
        $this->setData('media_gallery_id', null);
        $this->setData('id', null);
        $this->save();
    }

    // this is the full filesystem path to the resource
    // this is needed by the sync process
    // this is set by the varien_data_collection_filesystem
    public function getFilename()
    {
        return $this->getData('filename');
    }

    // we need this because of how a Varien_Data_Collection_Filesystem populates the data of the individual item records within it
    public function addData(array $arr)
    {
        parent::addData($arr);
        $this->setData('media_path', $this->getData('path') ?: $this->removeMediaPrefix($this->getData('filename')));
        return $this;
    }

    protected function removeMediaPrefix($imagePath) {
        if(0 === strpos($imagePath, \Mage::getBaseDir('media') . DS)) {
            return substr($imagePath, strlen(\Mage::getBaseDir('media') . DS));
        }
        return $imagePath;
    }

}