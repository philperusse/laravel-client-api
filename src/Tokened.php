<?php

namespace philperusse\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

abstract class Tokened extends Api
{
    protected $auth_key = 'Authorization';
    protected $auth_prefix = 'Bearer ';
    protected $auth_method = 'POST';

    protected $oauth_endpoint = '/login';
    protected $token_key = 'access_token';
    protected $expiration_key = 'expires_in';

    protected $auth_payload;
    protected $token_cache_key;

    public function call( $path, array $params = [], $method = 'GET') {

        $this->setRequestParams([
            'headers'        => [
                $this->auth_key => $this->auth_prefix . $this->token()
            ],
        ]);

        return parent::call( $path, $params, $method );
    }

    public function token()
    {
        return Cache::get($this->cacheKey(), function(){
            return $this->refreshToken();
        });
    }

    public function setAccessToken($value, $expires)
    {
        Cache::put($this->cacheKey(), $value, floor(($expires - 30)));
        return $value;
    }

    public function getAuthPayload()
    {
        return $this->auth_payload;
    }

    public function setAuthPayload($payload)
    {
        $this->auth_payload = $payload;
    }

    public function refreshToken()
    {
        $oldParams = $this->getRequestParams();

        $this->setRequestParams($this->getAuthPayload());

        $response = $this->execute($this->oauthEndpoint(), $this->auth_method);

        $this->setRequestParams($oldParams);

        return $this->setAccessToken($this->findAccessToken($response), $this->findExpired($response));
    }

    public function oauthEndpoint()
    {
        return $this->endpoint() . $this->oauth_endpoint;
    }

    protected function findAccessToken($response)
    {
        return data_get($response, $this->token_key);
    }

    /*
     * @return int minutes
     */
    protected function findExpired($response)
    {
        return data_get($response, $this->expiration_key);
    }

    protected function cacheKey()
    {
        if(null === $this->token_cache_key) {
            return Str::slug($this->endpoint());
        }

        return $this->token_cache_key;
    }

}