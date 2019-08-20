<?php

namespace philperusse\Api\Concerns;

trait Cacheable
{
    protected $cacheLifeTimeInMinutes = 0;
    protected $cacheLifeTimeInSeconds = 0;

    public function setCacheLifetime(int $minutes)
    {
        $this->cacheLifeTimeInMinutes = $minutes;
        $this->cacheLifeTimeInSeconds = $minutes * 60;
    }

    public function getCacheLifetime()
    {
        return $this->cacheLifeTimeInMinutes;
    }

    /*
     * Determine the cache name for the set of query properties given.
     */
    protected function determineCacheName(array $properties): string
    {
        return 'philperusse.api.'.md5(serialize($properties));
    }

}