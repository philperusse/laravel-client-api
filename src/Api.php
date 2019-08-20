<?php

namespace philperusse\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use philperusse\Api\Concerns\Cacheable;
use philperusse\Api\Concerns\CanLog;

abstract class Api
{
    protected $endpoint;
    protected $client;
    protected $requestParams;

    protected static $instance;

    use Cacheable,
        CanLog;

    public function __construct() {

        $this->setRequestParams([
            'base_uri' => $this->endpoint(),
            'headers' => [
                'Accept'    => 'application/json',
            ],
        ]);

        $this->client = new Client;
    }

    public static function init()
    {
        $api = new static();
        static::setInstance($api);
        return $api;
    }

    public static function instance()
    {
        return static::$instance;
    }

    public static function setInstance(Api $instance)
    {
        static::$instance = $instance;
    }

    public function endpoint($value = null)
    {
        if(null !== $value){
            return $this->endpoint = $value;
        }

        if(null === $this->endpoint) {
            throw new \Exception('You must implement the $endpoint property');
        }

        return $this->endpoint;
    }

    public function getRequestParams($key = null)
    {
        return data_get($this->requestParams, $key);
    }

    public function setRequestParams(array $params = [])
    {
        $this->requestParams = array_merge((array)$this->requestParams, (array) $params);
        return $this;
    }

    public function client()
    {
        return $this->client;
    }



    public function call($path, array $params = [], $method = 'GET')
    {
        $wrapper = $method === 'GET' || $method === 'POST' ? 'query' : 'json';
        $options = [
            $wrapper => $params,
        ];

        if($logger = $this->hasLogger()) {
            $options['handler'] = $this->createLoggingHandlerStack();
        }
        $this->setRequestParams($options);

        $cacheName = $this->determineCacheName(func_get_args());
        if ($this->cacheLifeTimeInSeconds == 0 || $method !== 'GET') {
            Cache::forget($cacheName);
        }

        return Cache::remember($cacheName, $this->cacheLifeTimeInSeconds, function () use ($path, $method) {
            return $this->execute($path, $method);
        });
    }

    protected function execute($url, $method = 'GET')
    {
        $response = $this->client()->request($method, $url, $this->getRequestParams());
        $body = json_decode((string) $response->getBody(), true);
        $response->getBody()->close();

        return $body;
    }



}
