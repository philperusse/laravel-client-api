<?php

namespace philperusse\Api;

class CollectionTransformer extends Transformer
{
    protected $prototype;
    protected $key;
    protected $metaKey;

    public function __construct($prototype, $key = null, $metaKey = null)
    {
        $this->prototype = $prototype;
        $this->key = $key;
        $this->metaKey = $metaKey;
    }

    /**
     * Returns transformed collection with instance of $this->prototype
     * If metaKey is specified, will return it as-is through the meta key.
     * @return \Illuminate\Support\Collection
     **/
    public function transform($results)
    {
        $data = data_get($results, $this->key, $this->prototype);

        $collection =  collect($data)->map(function($item) {
            return $this->createObject($item, $this->prototype );
        });

        return $this->metaKey
            ? collect(['data' => $collection])->concat([
                'meta' => data_get($results, $this->metaKey)
            ])
            : $collection;
    }


    public function __invoke($results)
    {
        return $this->transform($results);
    }
}