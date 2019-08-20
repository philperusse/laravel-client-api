<?php
namespace philperusse\Api\Exceptions;

use Exception;

class FailedParameterValidation extends Exception
{
    public static function invalidParameter($errors = [])
    {
        return new static($errors->first());
    }
}