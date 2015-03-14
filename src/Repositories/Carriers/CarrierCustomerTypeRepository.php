<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\CarrierCustomerType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Carriers\CarrierCustomerTypeRepositoryInterface as RepositoryInterface;

class CarrierCustomerTypeRepository extends IndexBasedResourceRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierCustomerType::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Carrier $carrier, CustomerType $type = null)
    {
        /** @var CarrierCustomerType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $carrier, $type, $type === null);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CarrierCustomerType $resource,
        Carrier $carrier = null,
        CustomerType $type = null,
        $isTypeEmpty = false
    ) {
        $input = [
            CarrierCustomerType::FIELD_ID_CARRIER => $carrier,
        ];
        if ($isTypeEmpty === false) {
            $idx = $type === null ? null : $type->{CustomerType::FIELD_ID};
            $resource->{CarrierCustomerType::FIELD_ID_CUSTOMER_TYPE} = $idx;
        }

        $this->fillModel($resource, $input);
    }
}
