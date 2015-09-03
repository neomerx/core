<?php namespace Neomerx\Core\Repositories\Stores;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class StoreRepository extends BaseRepository implements StoreRepositoryInterface
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
    public function createWithObjects(Address $address, array $attributes)
    {
        return $this->create($this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($addressId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($addressId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Store $resource, Address $address = null, array $attributes = [])
    {
        $this->update($resource, $this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(Store $resource, $addressId = null, array $attributes = [])
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($addressId));
    }

    /**
     * @param int $addressId
     *
     * @return array
     */
    protected function getRelationships($addressId)
    {
        return $this->filterNulls([
            Store::FIELD_ID_ADDRESS => $addressId,
        ]);
    }
}
