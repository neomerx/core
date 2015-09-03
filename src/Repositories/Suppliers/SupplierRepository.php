<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Supplier::class);
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
    public function updateWithObjects(Supplier $resource, Address $address = null, array $attributes = [])
    {
        $this->update($resource, $this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(Supplier $resource, $addressId = null, array $attributes = [])
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
            Supplier::FIELD_ID_ADDRESS => $addressId,
        ]);
    }
}
