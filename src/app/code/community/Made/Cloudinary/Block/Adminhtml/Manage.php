<?php

class Made_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_migrationTask;

    protected $_cloudinaryConfig;

    public function __construct()
    {
        $this->_blockGroup = 'made_cloudinary';

        $this->_controller = 'adminhtml_manage';

        $this->_headerText = Mage::helper('made_cloudinary')
            ->__('Manage Cloudinary');

        $this->_migrationTask = Mage::getModel('made_cloudinary/migration')
            ->load(Made_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);

        $this->_cloudinaryConfig = Mage::helper('made_cloudinary/configuration');

        parent::__construct();
    }

    public function getPercentComplete()
    {
        try {
            if ($this->getTotalImageCount() != 0) {
                return $this->getSynchronizedImageCount() * 100 / $this->getTotalImageCount();
            }
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function getSynchronizedImageCount()
    {
        return Mage::getResourceModel('made_cloudinary/synchronisation_collection')->getSize();
    }

    public function getTotalImageCount()
    {
        try {
            $collectionCounter = Mage::getModel('made_cloudinary/collectionCounter')
                ->addCollection(Mage::getResourceModel('made_cloudinary/media_collection'))
                ->addCollection(Mage::getResourceModel('made_cloudinary/cms_synchronisation_collection'));

            return $collectionCounter->count();
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function isExtensionEnabled()
    {
        return $this->_cloudinaryConfig->isEnabled();
    }

    public function allImagesSynced()
    {
        try {
            return $this->getSynchronizedImageCount() === $this->getTotalImageCount();
        } catch (Exception $e) {
            return false;
        }
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
        if ($this->_migrationTask->hasStarted()) {
            $startLabel = 'Stop Migration';
            $startAction = 'stopMigration';
        } else {
            $startLabel = 'Start Migration';
            $startAction = 'startMigration';
        }

        return $this->_makeButton($startLabel, $startAction, $this->allImagesSynced());
    }

    protected function _makeButton($label, $action, $disabled = false)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'cloudinary_migration_start',
                'label' => $this->helper('adminhtml')->__($label),
                'disabled' => $disabled,
                'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s', $action))}')"
            ));

        return $button->toHtml();
    }
} 
