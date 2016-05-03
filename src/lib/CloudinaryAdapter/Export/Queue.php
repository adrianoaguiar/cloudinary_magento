<?php

namespace CloudinaryAdapter\Export;

class Queue
{
    const MESSAGE_PROCESSING = 'Cloudinary export: processing';

    const MESSAGE_COMPLETE = 'Cloudinary export: complete';

    protected $exportTask;

    protected $syncedMediaRepo;

    protected $logger;

    protected $batchUploader;

    public function __construct(
        Task $exportTask,
        SyncedMediaRepo $syncedMediaRepo,
        BatchUploader $batchUploader,
        Logger $logger
    ) {
        $this->exportTask = $exportTask;
        $this->syncedMediaRepo = $syncedMediaRepo;
        $this->logger = $logger;
        $this->batchUploader = $batchUploader;
    }

    public function process()
    {
        if ($this->exportTask->hasBeenStopped()) {
            return;
        }

        $images = $this->syncedMediaRepo->findUnsyncedImages();

        if (!$images) {
            $this->logger->notice(self::MESSAGE_COMPLETE);
            $this->exportTask->stop();
        } else {
            $this->logger->notice(self::MESSAGE_PROCESSING);
            $this->batchUploader->uploadImages($images);
        }
    }
}
