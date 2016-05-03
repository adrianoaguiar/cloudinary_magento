<?php

use CloudinaryAdapter\CloudinaryImageProvider;
use CloudinaryAdapter\Export\BatchUploader;

class Made_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        Mage::helper('made_cloudinary/autoloader')->register();
    }

    public function exportImages()
    {
        $exportTask = Mage::getModel('made_cloudinary/export')
            ->load(Made_Cloudinary_Model_Export::CLOUDINARY_MIGRATION_ID);

        $batchUploader = new BatchUploader(
            CloudinaryImageProvider::fromConfig(Mage::helper('made_cloudinary/config')->buildConfig()),
            $exportTask,
            Mage::getModel('made_cloudinary/logger'),
            null
        );

        $combinedMediaRepo = new Made_Cloudinary_Model_SyncedMediaUnifier(
            array(
                Mage::getResourceModel('made_cloudinary/sync_collection'),
                Mage::getResourceModel('made_cloudinary/cms_sync_collection')
            )
        );

        $exportQueue = new \CloudinaryAdapter\Export\Queue(
            $exportTask,
            $combinedMediaRepo,
            $batchUploader,
            Mage::getModel('made_cloudinary/logger')
        );

        $exportQueue->process();

        return $this;
    }
}
