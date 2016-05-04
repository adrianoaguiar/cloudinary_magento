<?php

namespace CloudinaryAdapter\Export;

use CloudinaryAdapter\Image;
use CloudinaryAdapter\Image\Syncable;
use CloudinaryAdapter\ImageProvider;

class BatchUploader
{
    const MESSAGE_STATUS = 'Cloudinary export: %s images exported';

    const MESSAGE_UPLOADED = 'Cloudinary export: uploaded %s';

    const MESSAGE_UPLOAD_ERROR = 'Cloudinary export: %s trying to upload %s';

    protected $imageProvider;

    protected $baseMediaPath;

    protected $logger;

    protected $exportTask;

    protected $countExported = 0;

    public function __construct(ImageProvider $imageProvider, Task $exportTask, Logger $logger, $baseMediaPath)
    {
        $this->imageProvider = $imageProvider;
        $this->exportTask = $exportTask;
        $this->baseMediaPath = $baseMediaPath;
        $this->logger = $logger;
    }

    public function uploadImages(array $images)
    {
        $this->countExported = 0;

        foreach ($images as $image) {

            if ($this->exportTask->hasBeenStopped()) {
                break;
            }
            $this->uploadImage($image);
        }

        $this->logger->notice(sprintf(self::MESSAGE_STATUS, $this->countExported));
    }

    protected function getAbsolutePath(Syncable $image)
    {
        return sprintf('%s%s', $this->baseMediaPath, $image->getFilename());
    }

    protected function uploadImage(Syncable $image)
    {
        try {
            $this->imageProvider->upload(Image::fromPath($this->getAbsolutePath($image), \Mage::getBaseDir('media')));
            $image->tagAsSynced();
            $this->countExported++;
            $this->logger->notice(sprintf(self::MESSAGE_UPLOADED, $image->getFilename()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf(self::MESSAGE_UPLOAD_ERROR, $e->getMessage(), $image->getFilename()));
        }
    }

}
