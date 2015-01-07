<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Cache\Cache;
use \Neomerx\Core\Cache\Store;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Taxes\VariantFormatter;

class VariantCache extends Cache
{
    const BIND_NAME = __CLASS__;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(
            App::make(VariantCacheProvider::BIND_NAME),
            [App::make(VariantFormatter::BIND_NAME)],
            App::make(Store::BIND_NAME)
        );
    }
}
