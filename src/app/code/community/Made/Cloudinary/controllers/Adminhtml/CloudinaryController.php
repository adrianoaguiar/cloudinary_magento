<?php

class Made_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    protected $_migrationTask;

    protected $_cloudinaryConfig;

    public function preDispatch()
    {
        $this->_migrationTask = Mage::getModel('made_cloudinary/migration')->load(Made_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);
        $this->_cloudinaryConfig = Mage::helper('made_cloudinary/config');

        parent::preDispatch();
    }

    public function indexAction()
    {
        $layout = $this->loadLayout();

        if ($this->_migrationTask->hasStarted()) {
            $layout->_addContent($this->_buildMetaRefreshBlock());
        }

        $this->renderLayout();
    }

    public function startMigrationAction()
    {
        $this->_migrationTask->start();

        $this->_redirectToManageCloudinary();
    }

    public function stopMigrationAction()
    {
        $this->_migrationTask->stop();

        $this->_redirectToManageCloudinary();
    }

    public function enableCloudinaryAction()
    {
        $this->_cloudinaryConfig->enable();

        $this->_redirectToManageCloudinary();
    }

    public function disableCloudinaryAction()
    {
        $this->_cloudinaryConfig->disable();

        $this->_redirectToManageCloudinary();
    }

    protected function _redirectToManageCloudinary()
    {
        return $this->_redirect('*/cloudinary');
    }

    protected function _buildMetaRefreshBlock()
    {
        return $this->getLayout()->createBlock('core/text')->setText('<meta http-equiv="refresh" content="5">');
    }

}