<?php

namespace CloudinaryAdapter\Image;

/**
 * This is interface basically provides API for the BatchUploader to upload and tag Syncable Magento records
 * without being directly reliant on anything Magento-specific
 */
interface Syncable
{
    /**
     * @return string
     * used by BatchUploader::getAbsolutePath
     */
    public function getFilename();

    /**
     * @return void
     * used by BatchUploader::uploadImage
     */
    public function tagAsSynced();
} 