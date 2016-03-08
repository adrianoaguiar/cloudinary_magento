<?php

namespace spec\CloudinaryExtension\Export;

use CloudinaryExtension\Export\BatchUploader;
use CloudinaryExtension\Export\Logger;
use CloudinaryExtension\Export\Queue;
use CloudinaryExtension\Export\SyncedMediaRepo;
use CloudinaryExtension\Export\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueueSpec extends ObjectBehavior
{
    function let(
        Task $exportTask,
        SyncedMediaRepo $syncedMediaRepo,
        BatchUploader $batchUploader,
        Logger $logger
    ) {
        $this->beConstructedWith($exportTask, $syncedMediaRepo, $batchUploader, $logger);
    }

    function it_does_not_process_the_export_queue_if_task_has_been_stopped(
        Task $exportTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger
    ) {
        $exportTask->hasBeenStopped()->willReturn(true);

        $syncedMediaRepo->findUnsyncedImages()->shouldNotBeCalled();
        $logger->notice(Argument::any())->shouldNotBeCalled();

        $this->process();
    }


    function it_processes_the_export_queue_if_task_has_been_started(
        Task $exportTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $exportTask->hasBeenStopped()->willReturn(false);
        $exportTask->stop()->willReturn();

        $logger->notice(Queue::MESSAGE_PROCESSING)->shouldBeCalled();
        $syncedMediaRepo->findUnsyncedImages()->willReturn(array('image1', 'image2'));

        $batchUploader->uploadImages(array('image1', 'image2'))->shouldBeCalled();

        $this->process();
    }

    function it_stops_the_export_task_if_there_is_nothing_left_to_process(
        Task $exportTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $exportTask->hasBeenStopped()->willReturn(false);
        $syncedMediaRepo->findUnsyncedImages()->willReturn(array());

        $logger->notice(Queue::MESSAGE_COMPLETE)->shouldBeCalled();
        $exportTask->stop()->shouldBeCalled();

        $batchUploader->uploadImages(Argument::any())->shouldNotBeCalled();

        $this->process();
    }
}
