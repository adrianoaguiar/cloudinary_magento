<?php

namespace CloudinaryAdapter\Export;

interface SyncedMediaRepo
{
    public function findUnsyncedImages();
}
