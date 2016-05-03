<?php

namespace spec\CloudinaryAdapter;

use Cloudinary;
use CloudinaryAdapter\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function let(Config $config)
    {
        $config->build()->shouldBeCalled();
        $this->beConstructedThrough('fromConfig', [$config]);
    }

    function it_is_an_image_provider()
    {
        $this->shouldBeAnInstanceOf('CloudinaryAdapter\ImageProvider');
    }

    function it_sets_user_agent_string(Config $config)
    {
        $config->getUserPlatform()->willReturn('Test User Agent String');

        $this->getWrappedObject();
        expect(Cloudinary::$USER_PLATFORM)->toBe('Test User Agent String');
    }
}
