<?php namespace Neomerx\Core\Api\Stores;

use \Neomerx\Core\Models\Store;

interface StoresInterface
{
    /**
     * Get default store.
     *
     * @return Store
     */
    public function getDefault();
}
