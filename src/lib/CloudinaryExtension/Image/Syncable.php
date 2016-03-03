<?php

namespace CloudinaryExtension\Image;

interface Syncable
{
    public function getFilename();

    public function tagAsSynced();
} 