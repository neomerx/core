<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class TaxRepository extends CodeBasedResourceRepository implements TaxRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Tax::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Tax $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Tax $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function selectTaxes(
        $countryId,
        $regionId,
        $postcode,
        $customerTypeId,
        $productTaxTypeId
    ) {
        /** @var Tax $resource */
        $resource = $this->makeModel();
        return $resource->selectTaxes($countryId, $regionId, $postcode, $customerTypeId, $productTaxTypeId);
    }
}
