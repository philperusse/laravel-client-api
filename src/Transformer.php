<?php

namespace philperusse\Api;

class Transformer implements TransformerInterface
{
    public function createObject($data, AbstractCrudObject $prototype = null)
    {
        if (! $prototype) {
            return $data;
        }

        $object = clone $prototype;

        foreach($data as $key => $val) {
            $object->setAttribute($key, $val);
        }

        return $object;
    }
}