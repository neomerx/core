<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;

class AddressRepository extends IndexBasedResourceRepository implements AddressRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Address::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Region $region, array $attributes)
    {
        /** @var Address $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $region, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Address $resource, Region $region = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            Address::FIELD_ID_REGION => $region,
        ], $attributes);
    }
}
