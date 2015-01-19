<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class ManufacturerRepository extends CodeBasedResourceRepository implements ManufacturerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Manufacturer::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Address $address, array $attributes)
    {
        /** @var Manufacturer $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $address, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Manufacturer $resource, Address $address = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            Manufacturer::FIELD_ID_ADDRESS => $address,
        ], $attributes);
    }
}
