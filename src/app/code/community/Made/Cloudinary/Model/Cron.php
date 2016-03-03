<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Migration\BatchUploader;

class Made_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        Mage::helper('made_cloudinary/autoloader')->register();
    }

    public function migrateImages()
    {
        $migrationTask = Mage::getModel('made_cloudinary/migration')
            ->load(Made_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);

        $batchUploader = new BatchUploader(
            CloudinaryImageProvider::fromConfig(Mage::helper('made_cloudinary/config')->buildConfig()),
            $migrationTask,
            Mage::getModel('made_cloudinary/logger'),
            null
        );

        $combinedMediaRepo = new Made_Cloudinary_Model_SyncedMediaUnifier(
            array(
                Mage::getResourceModel('made_cloudinary/sync_collection'),
                Mage::getResourceModel('made_cloudinary/cms_sync_collection')
            )
        );

        $migrationQueue = new \CloudinaryExtension\Migration\Queue(
            $migrationTask,
            $combinedMediaRepo,
            $batchUploader,
            Mage::getModel('made_cloudinary/logger')
        );

        $migrationQueue->process();

        return $this;
    }
}
