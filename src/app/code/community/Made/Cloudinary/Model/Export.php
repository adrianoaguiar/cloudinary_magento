<?php

use CloudinaryAdapter\Export\Task;

class Made_Cloudinary_Model_Export extends Mage_Core_Model_Abstract implements Task
{
    const CLOUDINARY_MIGRATION_ID = 1;

    protected function _construct()
    {
        $this->_init('made_cloudinary/export');
    }

    public function hasStarted()
    {
        return (bool) $this->getStarted();
    }

    public function hasBeenStopped()
    {
        $this->load($this->getId());

        return (bool) $this->getStarted() == 0;
    }

    public function stop()
    {
        $this->setStarted(0);
        $this->save();
    }

    public function start()
    {
        $this->setStarted(1);
        $this->save();
    }
}