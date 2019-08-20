<?php

namespace philperusse\Api\Concerns;

use philperusse\Api\Exceptions\MissingAttribute;

trait NeedsAttribute
{
    public function needsMultipleAttributes(array $attributes, bool $mustBeFilled = true)
    {
        collect($attributes)->each(function($attribute) use ($mustBeFilled) {
            $this->needsAttribute($attribute, $mustBeFilled);
        });
    }

    public function needsAttribute(string $attribute, bool $mustBeFilled = true)
    {
        data_get($this->attributes, $attribute, function() use ($attribute) {
            throw MissingAttribute::attributeNotSpecified($attribute);
        });

        if($mustBeFilled)
            $this->attributeMustBeFilled($attribute);
    }

    public function attributeMustBeFilled(string $attribute)
    {
        $value = data_get($this->attributes, $attribute);
        if(! filled($value))
            throw MissingAttribute::attributeNotSpecified($attribute);
    }
}