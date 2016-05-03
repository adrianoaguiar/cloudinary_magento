<?php

namespace spec;

use CloudinaryAdapter\Image\Syncable;
use CloudinaryAdapter\Export\SyncedMediaRepo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Made_Cloudinary_Model_SyncedMediaUnifierSpec extends ObjectBehavior
{

    function let(
        SyncedMediaRepo $repositoryOne,
        SyncedMediaRepo $repositoryTwo
    )
    {
        $this->beConstructedWith(array($repositoryOne, $repositoryTwo));
    }

    function it_should_be_a_repository_of_synchronised_media()
    {
        $this->shouldHaveType('CloudinaryAdapter\Export\SyncedMediaRepo');
    }

    function it_should_combine_multiple_synchronised_media_repositories(
        $repositoryOne,
        $repositoryTwo,
        Syncable $syncableImageOne,
        Syncable $syncableImageTwo,
        Syncable $syncableImageThree,
        Syncable $syncableImageFour
    )
    {
        $repositoryOne->findUnsyncedImages()->willReturn(
            array(
                $syncableImageOne,
                $syncableImageTwo,
            )
        );
        $repositoryTwo->findUnsyncedImages()->willReturn(
            array(
                $syncableImageThree,
                $syncableImageFour,
            )
        );

        $this->findUnsyncedImages()->shouldReturn(
            array(
                $syncableImageOne,
                $syncableImageTwo,
                $syncableImageThree,
                $syncableImageFour
            )
        );
    }

    function it_should_return_no_items_if_all_repositories_have_been_synchronised(
        $repositoryOne,
        $repositoryTwo
    )
    {
        $repositoryOne->findUnsyncedImages()->willReturn(array());
        $repositoryTwo->findUnsyncedImages()->willReturn(array());

        $this->findUnsyncedImages()->shouldReturn(array());
    }

}