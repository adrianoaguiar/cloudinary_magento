<?php

class Made_Cloudinary_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{

    public function getMenuArray()
    {
        $menuArray = $this->_buildMenuArray();
        return $this->_addCloudinaryMediaLibraryUrlToMenu($menuArray);
    }

    protected function _addCloudinaryMediaLibraryUrlToMenu($menuArray)
    {
        $menuArray['made_cloudinary']['children']['console']['click'] =  sprintf(
            "window.open('%s')",
            Mage::helper('made_cloudinary/console')->getMediaLibraryUrl()
        );
        return $menuArray;
    }

}