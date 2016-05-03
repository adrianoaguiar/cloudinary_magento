<?php

namespace spec;

use PhpSpec\ObjectBehavior;

class Made_Cloudinary_Model_ExportSpec extends ObjectBehavior
{
    function it_should_be_a_export_task()
    {
        $this->shouldHaveType('CloudinaryAdapter\Export\Task');
    }
}