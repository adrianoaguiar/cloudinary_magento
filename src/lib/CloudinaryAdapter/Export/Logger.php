<?php

namespace CloudinaryAdapter\Export;

interface Logger
{
    public function warning($message, array $context = array());

    public function notice($message, array $context = array());

    public function error($message, array $context = array());
}
