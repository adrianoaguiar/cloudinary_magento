<?php

namespace CloudinaryExtension\Migration;

interface SyncedMediaRepo
{
    public function findUnsyncedImages();
}
