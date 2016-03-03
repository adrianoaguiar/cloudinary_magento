<?php

namespace Ui;

use Behat\Behat\Context\Context;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\CloudinaryEnvVar;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use ImageProviders\FakeImageProvider;
use MageTest\MagentoExtension\Context\RawMagentoContext;
use MageTest\Manager\FixtureManager;
use MageTest\Manager\Attributes\Provider\YamlProvider;
use Page\AdminLogin;
use Page\CloudinaryAdminSystemConfig;

class AdminCredentialsContext extends RawMagentoContext implements Context
{

    protected $imageProvider;
    protected $_fixtureManager;
    protected $image;
    protected $areCredentialsValid;
    protected $adminConfigPage;
    protected $adminLoginPage;

    public function __construct(CloudinaryAdminSystemConfig $adminSystemConfig, AdminLogin $adminLoginPage)
    {
        $this->adminConfigPage = $adminSystemConfig;
        $this->adminLoginPage = $adminLoginPage;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
      $this->_fixtureManager = new FixtureManager(new YamlProvider());
      $this->_fixtureManager->loadFixture('admin/user', __DIR__ . DS . '../Fixtures/Admin.yaml');
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->_fixtureManager->clear();
    }

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
    public function iHaveAnImage($anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage
     */
    public function iUploadTheImage(Image $anImage)
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->saveEnvVarToMagentoConfig($envVar);

        $this->imageProvider = new FakeImageProvider($envVar);

        $this->imageProvider->setMockCloud(Cloud::fromName('session-digital'));
        $this->imageProvider->setMockCredentials(Key::fromString('ABC123'), Secret::fromString('DEF456'));

        $this->imageProvider->upload($anImage);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->imageProvider->getImageUrlByName((string)$this->image))->notToBe('');
    }

    /**
     * @Given I have used a valid environment variable in the config
     */
    public function iHaveUsedAValidEnvVarInTheConfig()
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->imageProvider = new FakeImageProvider($envVar);
    }

    /**
     * @Given I have used an invalid environment variable in the config
     */
    public function iHaveUsedAnInvalidEnvVarInTheConfig()
    {
        $envVar = CloudinaryEnvVar::fromString('CLOUDINARY_URL=cloudinary://UVW789:XYZ123@session-digital');
        $this->imageProvider = new FakeImageProvider($envVar);
    }

    /**
     * @Given I have not configured my environment variable
     */
    public function iHaveNotConfiguredMyEnvVar()
    {
        $this->saveEnvVarToMagentoConfig('');
    }

    /**
     * @Given I have configured my environment variable
     */
    public function iHaveConfiguredMyEnvVar()
    {
        $this->saveEnvVarToMagentoConfig('anEnvVar');
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
        $this->imageProvider->setMockCloud(Cloud::fromName('session-digital'));
        $this->imageProvider->setMockCredentials(Key::fromString('ABC123'), Secret::fromString('DEF456'));

        $this->areCredentialsValid = $this->imageProvider->validateCredentials();
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

    /**
     * @Given I have not configured my cloud and credentials
     */
    public function iHaveNotConfiguredMyCloudAndCredentials()
    {
        $this->saveCredentialsAndCloudToMagentoConfig('', '', '');
    }

    /**
     * @When I go to the Cloudinary config
     */
    public function iGoToTheCloudinaryConfig()
    {
        $this->adminConfigPage->open();
    }

    /**
     * @Then I should be prompted to sign up to Cloudinary
     */
    public function iShouldBePromptedToSignUpToCloudinary()
    {
        expect($this->adminConfigPage->containsSignUpPrompt())->toBe(true);
    }

    /**
     * @Then I should not be prompted to sign up to Cloudinary
     */
    public function iShouldNotBePromptedToSignUpToCloudinary()
    {
        expect($this->adminConfigPage->containsSignUpPrompt())->toBe(false);
    }

    protected function saveEnvVarToMagentoConfig($envVar)
    {
        $this->adminLoginPage->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());

        $this->adminConfigPage->open();

        $this->adminConfigPage->enterEnvVar($envVar);
        $this->adminConfigPage->saveCloudinaryConfig();

    }

}
