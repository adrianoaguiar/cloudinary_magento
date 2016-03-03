<?php

namespace CloudinaryExtension\Migration;

class Queue
{
    const MESSAGE_PROCESSING = 'Cloudinary migration: processing';

    const MESSAGE_COMPLETE = 'Cloudinary migration: complete';

    protected $migrationTask;

    protected $syncedMediaRepo;

    protected $logger;

    protected $batchUploader;

    public function __construct(
        Task $migrationTask,
        SyncedMediaRepo $syncedMediaRepo,
        BatchUploader $batchUploader,
        Logger $logger
    ) {
        $this->migrationTask = $migrationTask;
        $this->syncedMediaRepo = $syncedMediaRepo;
        $this->logger = $logger;
        $this->batchUploader = $batchUploader;
    }

    public function process()
    {
        if ($this->migrationTask->hasBeenStopped()) {
            return;
        }

        $images = $this->syncedMediaRepo->findUnsyncedImages();

        if (!$images) {
            $this->logger->notice(self::MESSAGE_COMPLETE);
            $this->migrationTask->stop();
        } else {
            $this->logger->notice(self::MESSAGE_PROCESSING);
            $this->batchUploader->uploadImages($images);
        }
    }
}
