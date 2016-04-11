<?php

class Made_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    protected $_exportTask;

    protected $_cloudinaryConfig;

    public function preDispatch()
    {
        $this->_exportTask = Mage::getModel('made_cloudinary/export')->load(Made_Cloudinary_Model_Export::CLOUDINARY_MIGRATION_ID);
        $this->_cloudinaryConfig = Mage::helper('made_cloudinary/config');

        parent::preDispatch();
    }

    public function indexAction()
    {
        $layout = $this->loadLayout();

        if ($this->_exportTask->hasStarted()) {
            $layout->_addContent($this->_buildMetaRefreshBlock());
        }

        $this->renderLayout();
    }

    public function startExportAction()
    {
        $this->_exportTask->start();

        $this->_redirectToManageCloudinary();
    }

    public function stopExportAction()
    {
        $this->_exportTask->stop();

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
        return $this->getLayout()->createBlock('core/text')->setText('<meta http-equiv="refresh" content="10">');
    }

}