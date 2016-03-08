<?php

namespace CloudinaryExtension\Export;

interface SyncedMediaRepo
{
    public function findUnsyncedImages();
}
