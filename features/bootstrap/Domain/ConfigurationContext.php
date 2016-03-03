<?php

namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Config;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use ImageProviders\ConfigImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class ConfigContext implements Context
{
    protected $config;
    protected $imageProvider;

    /**
     * @Given I have a configuration to use multiple sub-domains
     */
    public function iHaveAConfigToUseMultipleSubDomains()
    {
        $cloud = Cloud::fromName('aCloud');
        $credentials = new Credentials(Key::fromString("aKey"), Secret::fromString("aSecret"));

        $this->config = Config::fromCloudAndCredentials($cloud, $credentials);
        $this->config->enableCdnSubdomain();
    }

    /**
     * @When I apply the configuration to the image provider
     */
    public function iApplyTheConfigToTheImageProvider()
    {
        $this->imageProvider = new ConfigImageProvider($this->config);
    }

    /**
     * @Then the image provider should use multiple sub-domains
     */
    public function theImageProviderShouldUseMultipleSubDomains()
    {
        $request1 = $this->imageProvider->transformImage(Image::fromPath('somePath'), Transformation::builder());
        $request2 = $this->imageProvider->transformImage(Image::fromPath('someOtherPath'), Transformation::builder());

        expect($this->requestPrefixIsTheSame($request1, $request2))->toBe(false);
    }

    protected function requestPrefixIsTheSame($request1, $request2)
    {
        return substr($request1, 0, 4) === substr($request2, 0, 4);
    }
}