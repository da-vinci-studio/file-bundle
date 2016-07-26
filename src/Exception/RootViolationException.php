<?php

namespace Dvs\FileBundle\Exception;

class RootViolationException extends \Exception
{
    /**
     * @return RootViolationException
     */
    public static function rootViolation()
    {
        return new self('Dir name empty');
    }
}
