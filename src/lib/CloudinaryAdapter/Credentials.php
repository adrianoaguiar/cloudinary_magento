<?php

namespace CloudinaryAdapter;


use CloudinaryAdapter\Security\Key;
use CloudinaryAdapter\Security\Secret;

class Credentials
{

    protected $key;
    protected $secret;

    public function __construct(Key $key,Secret $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
