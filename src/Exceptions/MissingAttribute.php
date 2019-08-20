<?php
namespace philperusse\Api\Exceptions;

use Exception;

class MissingAttribute extends Exception
{
	public static function attributeNotSpecified($attribute)
	{
		return new static('Missing attribute  ' . $attribute);
	}
}