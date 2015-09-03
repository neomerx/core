<?php namespace Neomerx\Core\Repositories\Addresses;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Address::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(array $attributes, Nullable $region = null)
    {
        return $this->create($attributes, $this->idOfNullable($region, Region::class));
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes, Nullable $regionId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($regionId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Address $resource, array $attributes = [], Nullable $region = null)
    {
        $this->update($resource, $attributes, $this->idOfNullable($region, Region::class));
    }

    /**
     * @inheritdoc
     */
    public function update(Address $resource, array $attributes = [], Nullable $regionId = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($regionId));
    }

    /**
     * @param Nullable $regionId
     *
     * @return array
     */
    protected function getRelationships(Nullable $regionId)
    {
        return $this->filterNulls([], [
            Address::FIELD_ID_REGION => $regionId,
        ]);
    }
}
