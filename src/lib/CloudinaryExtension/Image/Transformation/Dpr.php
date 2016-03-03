<?php

namespace CloudinaryExtension\Image\Transformation;

class Dpr
{
    protected $value;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromString($value)
    {
        return new Dpr($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
