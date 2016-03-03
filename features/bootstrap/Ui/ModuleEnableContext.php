<?php

namespace Ui;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use MageTest\MagentoExtension\Context\RawMagentoContext;
use Page\AdminLogin;
use Page\CloudinaryManagement;

class ModuleEnableContext extends RawMagentoContext implements Context, SnippetAcceptingContext
{

    protected $adminLogin;

    protected $cloudinaryManagement;

    public function __construct(AdminLogin $adminLogin, CloudinaryManagement $cloudinaryManagement)
    {
        $this->adminLogin = $adminLogin;
        $this->cloudinaryManagement = $cloudinaryManagement;
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        $this->adminLogin->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());
    }

    /**
     * @Given the Cloudinary module is disabled
     */
    public function theCloudinaryModuleIsDisabled()
    {
        \Mage::helper('made_cloudinary/config')->disable();
    }

    /**
     * @When I access the Cloudinary config
     */
    public function iAccessTheCloudinaryConfig()
    {
        $this->cloudinaryManagement->open();
    }

    /**
     * @Then I should be able to enable the module
     */
    public function iShouldBeAbleToEnableTheModule()
    {
        $this->cloudinaryManagement->enable();

        expect($this->cloudinaryManagement)->toHaveDisableButton();
    }

    /**
     * @Given the Cloudinary module is enabled
     */
    public function theCloudinaryModuleIsEnabled()
    {
        \Mage::helper('made_cloudinary/config')->enable();
    }

    /**
     * @Then I should be able to disable the module
     */
    public function iShouldBeAbleToDisableTheModule()
    {
        $this->cloudinaryManagement->disable();

        expect($this->cloudinaryManagement)->toHaveEnableButton();
    }
}
