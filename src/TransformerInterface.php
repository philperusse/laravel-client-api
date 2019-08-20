<?php

namespace philperusse\Api;

interface TransformerInterface
{
    public function createObject($data, AbstractCrudObject $prototype = null);
}