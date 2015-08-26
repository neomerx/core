<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CarrierRepository extends CodeBasedResourceRepository implements CarrierRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Carrier::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes, Currency $currency)
    {
        /** @var Carrier $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes, $currency);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Carrier $resource, array $attributes, Currency $currency = null)
    {
        $this->fillModel($resource, [
            Carrier::FIELD_ID_CURRENCY => $currency,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function selectCarriers(
        $countryId,
        $regionId,
        $postcode,
        $customerTypeId,
        $pkgWeight = null,
        $maxDimension = null,
        $pkgCost = null
    ) {
        /** @var Carrier $resource */
        $resource = $this->makeModel();
        return $resource->selectCarriers(
            $countryId,
            $regionId,
            $postcode,
            $customerTypeId,
            $pkgWeight,
            $maxDimension,
            $pkgCost
        );
    }
}
