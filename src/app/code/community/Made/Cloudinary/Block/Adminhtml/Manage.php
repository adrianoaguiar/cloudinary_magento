<?php

class Made_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_exportTask;
    protected $_cloudinaryConfig;
    protected $_productImageCount;      // don't load from db more than once
    protected $_cmsImageCount;          // ditto
    protected $_syncedProductImageCount;// ditto
    protected $_syncedCmsImageCount;    // ditto

    public function __construct()
    {
        $this->_blockGroup = 'made_cloudinary';
        $this->_controller = 'adminhtml_manage';
        $this->_headerText = Mage::helper('made_cloudinary')->__('Manage Cloudinary');
        $this->_exportTask = Mage::getModel('made_cloudinary/export')->load(Made_Cloudinary_Model_Export::CLOUDINARY_MIGRATION_ID);
        $this->_cloudinaryConfig = Mage::helper('made_cloudinary/config');
        parent::__construct();
    }

    public function allImagesSynced()
    {
        try {
            return $this->getSyncedImageCount() === $this->getTotalImageCount();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPercentComplete()
    {
        try {
            if ($this->getTotalImageCount() != 0) {
                return $this->getSyncedImageCount() * 100 / $this->getTotalImageCount();
            }
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function getSyncedImageCount()
    {
        return $this->getSyncedProductImageCount() + $this->getSyncedCmsImageCount();
    }

    public function getSyncedProductImageCount()
    {
        if(is_null($this->_syncedProductImageCount)) {
            try {
                $this->_syncedProductImageCount = Mage::getResourceModel('made_cloudinary/sync_collection')
                    ->addFieldToFilter('media_gallery_id', ['notnull' => true])->getSize();
            } catch (Exception $e) {
                $this->_syncedProductImageCount = 'Unknown';
            }
        }
        return $this->_syncedProductImageCount;
    }

    public function getSyncedCmsImageCount()
    {
        if(is_null($this->_syncedCmsImageCount)) {
            try {
                $this->_syncedCmsImageCount = Mage::getResourceModel('made_cloudinary/sync_collection')
                    ->addFieldToFilter('media_gallery_id', ['null' => true])->getSize();
            } catch (Exception $e) {
                $this->_syncedCmsImageCount = 'Unknown';
            }
        }
        return $this->_syncedCmsImageCount;
    }

    public function getTotalImageCount()
    {
        return $this->getProductImageCount() + $this->getCmsImageCount();
    }

    public function getProductImageCount()
    {
        if(is_null($this->_productImageCount)) {
            try {
                $this->_productImageCount = Mage::getResourceModel('made_cloudinary/media_collection')->getSize();
            } catch (Exception $e) {
                $this->_productImageCount = 'Unknown';
            }
        }
        return $this->_productImageCount;
    }

    public function getCmsImageCount()
    {
        if(is_null($this->_cmsImageCount)) {
            try {
                $this->_cmsImageCount = Mage::getResourceModel('made_cloudinary/cms_sync_collection')->getSize();
            } catch (Exception $e) {
                $this->_cmsImageCount = 'Unknown';
            }
        }
        return $this->_cmsImageCount;
    }

    public function isExtensionEnabled()
    {
        return $this->_cloudinaryConfig->isEnabled();
    }

    public function getEnableButton()
    {
        if ($this->_cloudinaryConfig->isEnabled()) {
            $enableLabel = 'Disable Cloudinary';
            $enableAction = 'disableCloudinary';
        } else {
            $enableLabel = 'Enable Cloudinary';
            $enableAction = 'enableCloudinary';
        }

        return $this->_makeButton($enableLabel, $enableAction);
    }

    public function getMigrateButton()
    {
        if ($this->_exportTask->hasStarted()) {
            $startLabel = 'Stop Export';
            $startAction = 'stopExport';
        } else {
            $startLabel = 'Start Export';
            $startAction = 'startExport';
        }

        return $this->_makeButton($startLabel, $startAction, $this->allImagesSynced());
    }

    protected function _makeButton($label, $action, $disabled = false)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'cloudinary_export_start',
                'label' => $this->helper('adminhtml')->__($label),
                'disabled' => $disabled,
                'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s', $action))}')"
            ));

        return $button->toHtml();
    }
} 
