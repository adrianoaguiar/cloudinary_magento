<?php

namespace CloudinaryAdapter\Security;

class Key
{

    protected $key;

    protected function __construct($key)
    {
        $this->key = (string)$key;
    }

    public static function fromString($aKey)
    {
        return new Key($aKey);
    }

    public function __toString()
    {
        return $this->key;
    }

}
