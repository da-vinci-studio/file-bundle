<?php

namespace Dvs\FileBundle\Exception;

class FileNotFoundException extends \Exception
{
    /**
     * @return FileNotFoundException
     */
    public static function fileNotFound()
    {
        return new self('File not found');
    }
}
