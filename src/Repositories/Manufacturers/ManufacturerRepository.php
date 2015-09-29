<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ManufacturerRepository extends BaseRepository implements ManufacturerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Manufacturer::class);
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
    public function updateWithObjects(Manufacturer $resource, Address $address = null, array $attributes = null)
    {
        $this->update($resource, $this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(Manufacturer $resource, $addressId = null, array $attributes = null)
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
            Manufacturer::FIELD_ID_ADDRESS => $addressId,
        ]);
    }
}
