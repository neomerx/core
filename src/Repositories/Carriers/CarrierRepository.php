<?php namespace Neomerx\Core\Repositories\Carriers;

use \DB;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CarrierRepository extends BaseRepository implements CarrierRepositoryInterface
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
    public function createWithObjects(array $attributes, Nullable $currency = null)
    {
        return $this->create($attributes, $this->idOfNullable($currency, Currency::class));
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes, Nullable $currencyId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($currencyId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Carrier $resource, array $attributes, Nullable $currency = null)
    {
        $this->update($resource, $attributes, $this->idOfNullable($currency, Currency::class));
    }

    /**
     * @inheritdoc
     */
    public function update(Carrier $resource, array $attributes, Nullable $currencyId = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($currencyId));
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
        return $this->convertStdClassesToModels(DB::select(
            'call spSelectCarriers(?, ?, ?, ?, ?, ?, ?)',
            [$countryId, $regionId, $postcode, $customerTypeId, $pkgWeight, $maxDimension, $pkgCost]
        ));
    }

    /**
     * @param Nullable|null $currencyId
     *
     * @return array
     */
    protected function getRelationships(Nullable $currencyId = null)
    {
        return $this->filterNulls([], [
            Carrier::FIELD_ID_CURRENCY => $currencyId,
        ]);
    }
}
