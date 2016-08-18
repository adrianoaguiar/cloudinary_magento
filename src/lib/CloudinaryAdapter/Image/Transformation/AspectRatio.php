<?php

namespace CloudinaryAdapter\Image\Transformation;

class AspectRatio
{
    protected $value;

    protected function __construct($value)
    {
        $this->value = $value ?: null;
    }

    public function getAspectRatio()
    {
        return $this->value;
    }

    public static function fromString($value)
    {
        return new AspectRatio($value);
    }

    public static function null()
    {
        return new AspectRatio(null);
    }

    public function __toString()
    {
        return $this->value;
    }
}
