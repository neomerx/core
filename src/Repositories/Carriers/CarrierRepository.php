<?php namespace Neomerx\Core\Repositories\Carriers;

use \DB;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Currency;
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
    public function createWithObjects(Currency $currency, array $attributes)
    {
        return $this->create($this->idOf($currency), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($currencyId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($currencyId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Carrier $resource, Currency $currency = null, array $attributes = null)
    {
        $this->update($resource, $this->idOf($currency), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(Carrier $resource, $currencyId = null, array $attributes = [])
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
     * @param int|null $currencyId
     *
     * @return array
     */
    protected function getRelationships($currencyId)
    {
        return $this->filterNulls([
            Carrier::FIELD_ID_CURRENCY => $currencyId,
        ]);
    }
}
