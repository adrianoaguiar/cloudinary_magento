<?php

use CloudinaryExtension\Migration\SyncedMediaRepo;

class Made_Cloudinary_Model_SyncedMediaUnifier implements SyncedMediaRepo
{

    protected $_syncedMediaRepositories;
    protected $_unsyncedImages = array();

    public function __construct(array $syncedMediaRepositories)
    {
        $this->_syncedMediaRepositories = $syncedMediaRepositories;
    }

    public function findUnsyncedImages($limit = 200)
    {
        foreach ($this->_syncedMediaRepositories as $syncedMediaRepo) {
            $this->_unsynsedImages = array_merge(
                $this->_unsyncsedImages,
                $syncedMediaRepo->findUnsyncedImages()
            );
        }
        return array_slice($this->_unsyncedImages, 0, $limit);
    }

}