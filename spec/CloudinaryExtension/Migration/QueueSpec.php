<?php

namespace spec\CloudinaryExtension\Migration;

use CloudinaryExtension\Migration\BatchUploader;
use CloudinaryExtension\Migration\Logger;
use CloudinaryExtension\Migration\Queue;
use CloudinaryExtension\Migration\SyncedMediaRepo;
use CloudinaryExtension\Migration\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueueSpec extends ObjectBehavior
{
    function let(
        Task $migrationTask,
        SyncedMediaRepo $syncedMediaRepo,
        BatchUploader $batchUploader,
        Logger $logger
    ) {
        $this->beConstructedWith($migrationTask, $syncedMediaRepo, $batchUploader, $logger);
    }

    function it_does_not_process_the_migration_queue_if_task_has_been_stopped(
        Task $migrationTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger
    ) {
        $migrationTask->hasBeenStopped()->willReturn(true);

        $syncedMediaRepo->findUnsyncedImages()->shouldNotBeCalled();
        $logger->notice(Argument::any())->shouldNotBeCalled();

        $this->process();
    }


    function it_processes_the_migration_queue_if_task_has_been_started(
        Task $migrationTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $migrationTask->hasBeenStopped()->willReturn(false);
        $migrationTask->stop()->willReturn();

        $logger->notice(Queue::MESSAGE_PROCESSING)->shouldBeCalled();
        $syncedMediaRepo->findUnsyncedImages()->willReturn(array('image1', 'image2'));

        $batchUploader->uploadImages(array('image1', 'image2'))->shouldBeCalled();

        $this->process();
    }

    function it_stops_the_migration_task_if_there_is_nothing_left_to_process(
        Task $migrationTask,
        SyncedMediaRepo $syncedMediaRepo,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $migrationTask->hasBeenStopped()->willReturn(false);
        $syncedMediaRepo->findUnsyncedImages()->willReturn(array());

        $logger->notice(Queue::MESSAGE_COMPLETE)->shouldBeCalled();
        $migrationTask->stop()->shouldBeCalled();

        $batchUploader->uploadImages(Argument::any())->shouldNotBeCalled();

        $this->process();
    }
}
