<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Config;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Gravity;
use CloudinaryExtension\Image\Transformation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Config
 */
class ConfigSpec extends ObjectBehavior
{
    function let(Cloud $cloud, Credentials $credentials)
    {
        $this->beConstructedThrough('fromCloudAndCredentials', array($cloud, $credentials));
    }

    function it_has_a_default_transformation()
    {
        $transformation = $this->getDefaultTransformation();

        $transformation->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
    }

    function it_sets_the_cdn_subdomain_flag()
    {
        $this->enableCdnSubdomain();
        $this->getCdnSubdomainStatus()->shouldBe(true);
    }
}
