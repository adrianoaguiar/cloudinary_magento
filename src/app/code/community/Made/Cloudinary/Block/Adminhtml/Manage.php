<?php

class Made_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_exportTask;
    protected $_cloudinaryConfig;
    protected $_totalProductImageCount; // no point getting this more than once
    protected $_totalCmsImageCount;     // ditto
    protected $_syncedImageCount;       // ditto

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
        Mage::log($this->getTotalImageCount());
        Mage::log($this->getSyncedImageCount() * 100);
        Mage::log($this->getTotalImageCount());

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
        if(is_null($this->_syncedImageCount)) {
            $this->_syncedImageCount = Mage::getResourceModel('made_cloudinary/sync_collection')->getSize();
        }
        return $this->_syncedImageCount;
    }

    public function getTotalImageCount()
    {
        return $this->getTotalProductImageCount() + $this->getTotalCmsImageCount();
    }

    public function getTotalProductImageCount()
    {
        if(is_null($this->_totalProductImageCount)) {
            try {
                $this->_totalProductImageCount = Mage::getResourceModel('made_cloudinary/media_collection')->getSize();
            } catch (Exception $e) {
                $this->_totalProductImageCount = 'Unknown';
            }
        }
        return $this->_totalProductImageCount;
    }

    public function getTotalCmsImageCount()
    {
        if(is_null($this->_totalCmsImageCount)) {
            try {
                $this->_totalCmsImageCount = Mage::getResourceModel('made_cloudinary/cms_sync_collection')->getSize();
            } catch (Exception $e) {
                $this->_totalCmsImageCount = 'Unknown';
            }
        }
        return $this->_totalCmsImageCount;
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
