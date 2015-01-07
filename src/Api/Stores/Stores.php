<?php namespace Neomerx\Core\Api\Stores;

use \Neomerx\Core\Models\Store;

class Stores implements StoresInterface
{
    const EVENT_PREFIX = 'Api.Store.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Store
     */
    private $storeModel;

    public function __construct(Store $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    /**
     * @return Store
     */
    public function getDefault()
    {
        return $this->storeModel->selectByCode(Store::DEFAULT_CODE)->withAddress()->firstOrFail();
    }
}
