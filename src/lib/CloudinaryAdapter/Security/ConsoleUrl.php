<?php

namespace CloudinaryAdapter\Security;

class ConsoleUrl
{

    protected $consoleUrl;

    const CLOUDINARY_CONSOLE_BASE_URL = 'https://cloudinary.com/console/';

    protected function __construct($path)
    {
        $this->consoleUrl = self::CLOUDINARY_CONSOLE_BASE_URL . $path;
    }

    public static function fromPath($path)
    {
        return new ConsoleUrl($path);
    }

    public function __toString()
    {
        return $this->consoleUrl;
    }
}
