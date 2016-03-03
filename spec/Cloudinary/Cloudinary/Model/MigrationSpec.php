<?php

namespace spec;

use PhpSpec\ObjectBehavior;

class Made_Cloudinary_Model_MigrationSpec extends ObjectBehavior
{
    function it_should_be_a_migration_task()
    {
        $this->shouldHaveType('CloudinaryExtension\Migration\Task');
    }
}