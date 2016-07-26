<?php

namespace Dvs\FileBundle\Exception;

class FileExistsException extends \Exception
{
    /**
     * @return FileExistsException
     */
    public static function fileExists()
    {
        return new self('File already exists');
    }
}
