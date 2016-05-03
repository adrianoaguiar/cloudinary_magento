<?php

namespace CloudinaryAdapter\Export;

interface Task
{
    public function hasStarted();

    public function hasBeenStopped();

    public function stop();

    public function start();
}
