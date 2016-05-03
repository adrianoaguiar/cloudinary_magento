<?php

use CloudinaryAdapter\Export\SyncedMediaRepo;

class Made_Cloudinary_Model_SyncedMediaUnifier implements SyncedMediaRepo
{

    protected $_syncedMediaRepos;
    protected $_unsyncedImages = array();

    public function __construct(array $repos)
    {
        $this->_syncedMediaRepos = $repos;
    }

    public function findUnsyncedImages($limit = 200)
    {
        foreach ($this->_syncedMediaRepos as $repo) {
            $this->_unsyncedImages = array_merge(
                $this->_unsyncedImages,
                $repo->findUnsyncedImages()
            );
        }
        return array_slice($this->_unsyncedImages, 0, $limit);
    }

}