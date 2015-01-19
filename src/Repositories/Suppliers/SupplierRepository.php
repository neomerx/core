<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class SupplierRepository extends CodeBasedResourceRepository implements SupplierRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Supplier::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Address $address, array $attributes)
    {
        /** @var Supplier $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $address, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Supplier $resource, Address $address = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            Supplier::FIELD_ID_ADDRESS => $address,
        ], $attributes);
    }
}
