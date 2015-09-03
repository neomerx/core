<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class BaseProductRepository extends BaseRepository implements BaseProductRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(BaseProduct::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(
        Manufacturer $manufacturer,
        Currency $currency,
        array $attributes
    ) {
        return $this->create($this->idOf($manufacturer), $this->idOf($currency), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create(
        $manufacturerId,
        $currencyId,
        array $attributes
    ) {
        $resource = $this->createWith($attributes, $this->getRelationships($manufacturerId, $currencyId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        BaseProduct $base,
        Manufacturer $manufacturer = null,
        Currency $currency = null,
        array $attributes = null
    ) {
        $this->update($base, $this->idOf($manufacturer), $this->idOf($currency), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        BaseProduct $base,
        $manufacturerId = null,
        $currencyId = null,
        array $attributes = null
    ) {
        $this->updateWith($base, $attributes, $this->getRelationships($manufacturerId, $currencyId));
    }

    /**
     * @param int $manufacturerId
     * @param int $currencyId
     *
     * @return array
     */
    protected function getRelationships($manufacturerId, $currencyId)
    {
        return $this->filterNulls([
            BaseProduct::FIELD_ID_CURRENCY     => $currencyId,
            BaseProduct::FIELD_ID_MANUFACTURER => $manufacturerId,
        ]);
    }
}
