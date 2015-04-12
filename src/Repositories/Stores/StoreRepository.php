<?php namespace Neomerx\Core\Repositories\Stores;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class StoreRepository extends CodeBasedResourceRepository implements StoreRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Store::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Address $address, array $attributes)
    {
        /** @var Store $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $address, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Store $resource, Address $address = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            Store::FIELD_ID_ADDRESS => $address,
        ], $attributes);
    }
}
