<?php namespace Neomerx\Core\Repositories\Taxes;

use \DB;
use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class TaxRepository extends BaseRepository implements TaxRepositoryInterface
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
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Tax $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
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
        return $this->convertStdClassesToModels(DB::select(
            'call spSelectTaxes(?, ?, ?, ?, ?)',
            [$countryId, $regionId, $postcode, $customerTypeId, $productTaxTypeId]
        ));
    }
}
