<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CarrierPostcode;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CarrierPostcodeRepository extends IndexBasedResourceRepository implements CarrierPostcodeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierPostcode::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Carrier $carrier, array $attributes = null)
    {
        /** @var CarrierPostcode $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $carrier, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(CarrierPostcode $resource, Carrier $carrier = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            CarrierPostcode::FIELD_ID_CARRIER => $carrier,
        ], $attributes);
    }
}
