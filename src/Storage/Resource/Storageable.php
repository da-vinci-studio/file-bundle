<?php

namespace Dvs\FileBundle\Storage\Resource;

interface Storageable
{
    /**
     * @param string $name
     */
    public function setFileName(string $name);

    /**
     * @return mixed
     */
    public function getFileName();
}
