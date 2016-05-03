<?php

namespace spec\CloudinaryAdapter\Export;

use CloudinaryAdapter\Image;
use CloudinaryAdapter\Image\Syncable;
use CloudinaryAdapter\ImageProvider;
use CloudinaryAdapter\Export\BatchUploader;
use CloudinaryAdapter\Export\Logger;
use CloudinaryAdapter\Export\MediaResolver;
use CloudinaryAdapter\Export\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchUploaderSpec extends ObjectBehavior
{
    function let(
        ImageProvider $imageProvider,
        Task $exportTask,
        Logger $logger,
        Syncable $image1,
        Syncable $image2)
    {
        $this->beConstructedWith($imageProvider, $exportTask, $logger, '/catalog/media');

        $image1->tagAsSynced()->willReturn();
        $image2->tagAsSynced()->willReturn();
        $exportTask->hasBeenStopped()->willReturn(false, false);
    }

    function it_uploads_and_synchronizes_a_collection_of_images(
        ImageProvider $imageProvider,
        Logger $logger,
        Syncable $image1,
        Syncable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/r/b/image2.jpg');

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload(Image::fromPath('/catalog/media/z/b/image1.jpg'))->shouldHaveBeenCalled();
        $imageProvider->upload(Image::fromPath('/catalog/media/r/b/image2.jpg'))->shouldHaveBeenCalled();

        $image1->tagAsSynced()->shouldHaveBeenCalled();
        $image2->tagAsSynced()->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/z/b/image1.jpg'))->shouldHaveBeenCalled();
        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/r/b/image2.jpg'))->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 2))->shouldHaveBeenCalled();
    }

    function it_logs_an_error_if_any_of_the_image_uploads_fails(
        ImageProvider $imageProvider,
        Logger $logger,
        Syncable $image1,
        Syncable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/invalid');

        $exception = new \Exception('Invalid file');

        $images = array($image1, $image2);

        $imageProvider->upload(Image::fromPath('/catalog/media/invalid'))->willThrow($exception);
        $imageProvider->upload(Image::fromPath('/catalog/media/z/b/image1.jpg'))->shouldBeCalled();

        $this->uploadImages($images);

        $image1->tagAsSynced()->shouldHaveBeenCalled();
        $image2->tagAsSynced()->shouldNotHaveBeenCalled();

        $logger->error(
            sprintf(BatchUploader::MESSAGE_UPLOAD_ERROR, $exception->getMessage(), '/invalid')
        )->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1))->shouldHaveBeenCalled();
    }


    function it_stops_the_upload_process_if_task_is_stopped(
        ImageProvider $imageProvider,
        Task $exportTask,
        Logger $logger,
        Syncable $image1,
        Syncable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/invalid');

        $exportTask->hasBeenStopped()->willReturn(false, true);

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload('/catalog/media/z/b/image1.jpg')->shouldHaveBeenCalled();
        $image1->tagAsSynced()->shouldHaveBeenCalled();

        $imageProvider->upload('/catalog/media/r/b/image2.jpg')->shouldNotHaveBeenCalled();
        $image2->tagAsSynced()->shouldNotHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1))->shouldHaveBeenCalled();
    }
}

