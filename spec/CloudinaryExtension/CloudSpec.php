<?php

namespace spec\CloudinaryAdapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('fromName', ['cloud_name']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CloudinaryAdapter\Cloud');
    }
}
