<?php

namespace Dvs\FileBundle\Storage\Exception;

class NameGenerationException extends \DomainException
{
    /**
     * @return NameGenerationException
     */
    public static function notGenerated()
    {
        return new self('An error occurred during name generation');
    }
}
