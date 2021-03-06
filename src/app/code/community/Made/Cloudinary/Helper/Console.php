<?php

use CloudinaryAdapter\Security\ConsoleUrl;
use CloudinaryAdapter\Security\SignedConsoleUrl;

class Made_Cloudinary_Helper_Console extends Mage_Core_Helper_Abstract
{

    public function getMediaLibraryUrl()
    {
        $consoleUrl = ConsoleUrl::fromPath("media_library/cms");
        return (string)SignedConsoleUrl::fromConsoleUrlAndCredentials(
            $consoleUrl,
            Mage::helper('made_cloudinary/config')->buildCredentials()
        );

    }

}