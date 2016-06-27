<?php

namespace CloudinaryAdapter\Image\Transformation;

class Signature
{
    protected $value;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromString($value)
    {
        return new Signature($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
