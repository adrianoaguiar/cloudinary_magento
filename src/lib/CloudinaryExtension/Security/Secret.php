<?php

namespace CloudinaryExtension\Security;

class Secret
{

    protected $secret;

    protected function __construct($secret)
    {
        $this->secret = (string)$secret;
    }

    public static function fromString($aSecret)
    {
        return new Secret($aSecret);
    }

    public function __toString()
    {
        return $this->secret;
    }
}
