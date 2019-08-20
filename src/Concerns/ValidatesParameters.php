<?php

namespace philperusse\Api\Concerns;

use Validator;
use philperusse\Api\Exceptions\FailedParameterValidation;

trait ValidatesParameters
{
    protected function validateParameters($params, array $rules = [])
    {
        // Give a chance to temporarily override rules.
        $rules = array_merge(($this->parameterRules ?? []), $rules);
        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            throw FailedParameterValidation::invalidParameter($validator->errors());
        }
    }
}