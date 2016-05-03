<?php

namespace CloudinaryAdapter\Image\Transformation;

class Format
{
    const FETCH_FORMAT_AUTO = 'auto';

    protected $value;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromExtension($value)
    {
        return new Format($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
