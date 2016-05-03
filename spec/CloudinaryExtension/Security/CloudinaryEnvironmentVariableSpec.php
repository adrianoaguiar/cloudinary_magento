<?php

namespace spec\CloudinaryAdapter\Security;

use CloudinaryAdapter\Cloud;
use CloudinaryAdapter\Credentials;
use CloudinaryAdapter\Security\Key;
use CloudinaryAdapter\Security\Secret;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryEnvVarSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('fromString', array('CLOUDINARY_URL=cloudinary://aKey:aSecret@aCloud'));
    }

    function it_should_extract_the_cloud_name_from_the_environment_variable()
    {
        $this->getCloud()->shouldBeLike(Cloud::fromName('aCloud'));
    }

    function it_should_extract_the_credentials_from_the_environment_variable()
    {
        $credentials = new Credentials(Key::fromString('aKey'), Secret::fromString('aSecret'));
        $this->getCredentials()->shouldBeLike($credentials);
    }

}
