<?php

namespace spec\CloudinaryAdapter;

use CloudinaryAdapter\Cloud;
use CloudinaryAdapter\Config;
use CloudinaryAdapter\Credentials;
use CloudinaryAdapter\Image\Gravity;
use CloudinaryAdapter\Image\Transformation;
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

        $transformation->shouldBeAnInstanceOf('CloudinaryAdapter\Image\Transformation');
    }

    function it_sets_the_cdn_subdomain_flag()
    {
        $this->enableCdnSubdomain();
        $this->getCdnSubdomainStatus()->shouldBe(true);
    }
}
