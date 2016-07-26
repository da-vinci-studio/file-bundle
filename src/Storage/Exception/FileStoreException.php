<?php

namespace Dvs\FileBundle\Storage\Exception;

class FileStoreException extends \DomainException
{
    /**
     * @return FileStoreException
     */
    public static function notStored()
    {
        return new self('File not stored by filesystem abstraction layer');
    }
}
