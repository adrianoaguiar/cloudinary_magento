<?php


class Made_Cloudinary_Helper_Autoloader
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryAdapter';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    protected $_originalAutoloaders;

    protected $_registerHasRun;


    public function register()
    {
        if(Mage::helper('core')->isModuleEnabled('Cloudinary_Cloudinary')) {
            return; // we want these modules to live happily side-by-side, the respective autoloaders do the same thing
        }

        if(!is_null($this->_registerHasRun)) {
            return;
        }

        $this->_deregisterVarienAutoloaders();
        $this->_registerCloudinaryAutoloader();
        $this->_registerCloudinaryAdapterAutoloader();
        $this->_reregisterVarienAutoloaders();

        $this->_registerHasRun = true;
    }

    protected function _registerCloudinaryAdapterAutoloader()
    {
        spl_autoload_register(
            function ($className) {
                if(
                    strpos($className, Made_Cloudinary_Helper_Autoloader::CLOUDINARY_EXTENSION_LIB_PATH . '\\') === 0 ||
                    strpos($className, Made_Cloudinary_Helper_Autoloader::CLOUDINARY_LIB_PATH . '\\') === 0
                ) {
                    include_once preg_replace(Made_Cloudinary_Helper_Autoloader::CONVERT_CLASS_TO_PATH_REGEX, '/', $className) . '.php';
                }
            }
        );

        return $this;
    }

    protected function _registerCloudinaryAutoloader()
    {
        $libFolder = Mage::getBaseDir('lib');

        spl_autoload_register(
            function ($className) use ($libFolder) {
                if($className ===  Made_Cloudinary_Helper_Autoloader::CLOUDINARY_LIB_PATH) {
                    foreach(new GlobIterator($libFolder . DS . Made_Cloudinary_Helper_Autoloader::CLOUDINARY_LIB_PATH . DS . '*.php') as $phpFile) {
                        include_once $phpFile;
                    }
                }
            }
        );

        return $this;
    }

    protected function _deregisterVarienAutoloaders()
    {
        $this->_originalAutoloaders = array();

        foreach (spl_autoload_functions() as $callback) {
            if (is_array($callback) && $callback[0] instanceof Varien_Autoload) {
                $this->_originalAutoloaders[] = $callback;
                spl_autoload_unregister($callback);
            }
        }
    }

    protected function _reregisterVarienAutoloaders()
    {
        foreach ($this->_originalAutoloaders as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }
} 