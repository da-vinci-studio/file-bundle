<?php

namespace Dvs\FileBundle\Storage\Exception;

class PathGenerationException extends \DomainException
{
    /**
     * @return PathGenerationException
     */
    public static function emptyFileName()
    {
        return new self('File name can not be an empty string');
    }
}
