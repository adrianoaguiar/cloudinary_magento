<?php


namespace Domain;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use CloudinaryAdapter\Credentials;
use CloudinaryAdapter\Image\Transformation;
use CloudinaryAdapter\Security\CloudinaryEnvVar;
use CloudinaryAdapter\Security\Key;
use CloudinaryAdapter\Security\Secret;
use CloudinaryAdapter\Image;
use CloudinaryAdapter\Cloud;
use ImageProviders\FakeImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    protected $provider;
    protected $image;
    protected $areCredentialsValid;


    /**
     * @Transform :anImage
     */
    public function transformStringToAnImage($string)
    {
        return Image::fromPath($string);
    }

    /**
     * @Given I have an image :anImage
     */
    public function iHaveAnImage(Image $anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage
     */
    public function iUploadTheImage(Image $anImage)
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->provider = new FakeImageProvider($envVar);

        $cloud = Cloud::fromName('session-digital');
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $this->provider->setMockCloud($cloud);
        $this->provider->setMockCredentials($key, $secret);

        $this->provider->upload($anImage);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->provider->getImageUrlByName($this->getImageName()))->notToBe('');
    }

    protected function getImageName()
    {
        $imagePath = explode(DS, $this->image);
        return $imagePath[count($imagePath) - 1];
    }

    /**
     * @Given I have used a valid environment variable in the configuration
     */
    public function iHaveUsedAValidEnvVarInTheConfig()
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->provider = new FakeImageProvider($envVar);
    }

    /**
     * @Given I have used an invalid environment variable in the configuration
     */
    public function iHaveUsedAnInvalidEnvVarInTheConfig()
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://UVW789:XYZ123@session-digital');
        $this->provider = new FakeImageProvider($envVar);
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
        $cloud = Cloud::fromName('session-digital');
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $this->provider->setMockCloud($cloud);
        $this->provider->setMockCredentials($key, $secret);

        $this->areCredentialsValid = $this->provider->validateCredentials();
    }

    /**
     * @Then I should be informed my credentials are valid
     */
    public function iShouldBeInformedMyCredentialsAreValid()
    {
        expect($this->areCredentialsValid)->toBe(true);
    }

    /**
     * @Then I should be informed that my credentials are not valid
     */
    public function iShouldBeInformedThatMyCredentialsAreNotValid()
    {
        expect($this->areCredentialsValid)->toBe(false);
    }
}
