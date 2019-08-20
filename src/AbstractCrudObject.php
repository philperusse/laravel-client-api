<?php

namespace philperusse\Api;

class AbstractCrudObject
{
    protected $api;
    protected $attributes;

    public function __construct(Api $api = null, $attributes = [])
    {
        $this->api = static::assureApi($api);

        $this->attributesOrID($attributes);
        $this->setAttributes($attributes);
    }

    public function call($path, $prototype = null, $params = [], $method = 'GET')
    {
        $response = $this->getApi()->call($path, $params, $method);

        if($prototype instanceof TransformerInterface || is_callable($prototype)) {
            return $prototype($response);
        }

        return (new Transformer)->createObject($response, $prototype);
    }

    public function getApi() {
        return $this->api;
    }

    public function setApi($api) {
        $this->api = $api;
    }

    public static function assureApi(Api $instance = null)
    {
        $instance = $instance ?: Api::instance();
        if (! $instance) {
            throw new \InvalidArgumentException(
                'An Api instance must be provided as argument or '.
                'set as instance');
        }

        return $instance;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function setAttributes($attributes = [])
    {
        if(!is_array($attributes))
            return $this;

        foreach($attributes as $key => $attribute) {
            $this->setAttribute($key, $attribute);
        }

        return $this;
    }

    public function getAttribute($key, $default = null)
    {
        return array_key_exists($key, $this->attributes) ?
            $this->attributes[$key] : $default;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    protected function attributesOrID($attributes)
    {
        if(is_array($attributes) || empty($attributes) )
            return;

        $this->setAttribute('id', $attributes);
    }


}