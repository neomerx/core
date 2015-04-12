<?php namespace Neomerx\Core\Repositories\Addresses;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class AddressRepository extends IndexBasedResourceRepository implements AddressRepositoryInterface
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
    public function instance(array $attributes, Region $region = null)
    {
        /** @var Address $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes, $region);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Address $resource, array $attributes = null, Region $region = null)
    {
        $this->fillModel($resource, [
            Address::FIELD_ID_REGION => $region,
        ], $attributes);
    }
}
