<?php

namespace Dvs\FileBundle\Storage\Naming;

use Dvs\FileBundle\Storage\Resource\Storageable;

interface NamingStrategy
{
    /**
     * @param Storageable $storageable
     * 
     * @return string
     */
    public function generate(Storageable $storageable): string;
}
