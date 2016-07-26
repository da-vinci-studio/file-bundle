<?php

namespace Dvs\FileBundle\Storage\PathGenerating;

interface FilePathStrategy
{
    /**
     * @param string $fileName
     * 
     * @return string
     */
    public function generate(string $fileName): string;
}
