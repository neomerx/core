<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CarrierTerritory;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CarrierTerritoryRepository extends BaseRepository implements CarrierTerritoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierTerritory::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Carrier $carrier, BaseModel $territory)
    {
        return $this->create($this->idOf($carrier), get_class($territory), $this->idOf($territory));
    }

    /**
     * @inheritdoc
     */
    public function create($carrierId, $territoryType, $territoryId)
    {
        $territoryType = $this->getTerritoryType($territoryType);

        $resource = $this->createWith([], $this->filterNulls([
            CarrierTerritory::FIELD_ID_CARRIER     => $carrierId,
            CarrierTerritory::FIELD_TERRITORY_TYPE => $territoryType,
            CarrierTerritory::FIELD_TERRITORY_ID   => $territoryId,
        ]));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(CarrierTerritory $resource, Carrier $carrier = null, BaseModel $territory = null)
    {
        $territoryType = $territory === null ? null : get_class($territory);
        $territoryId   = $this->idOfNullable($territory);

        $this->update($resource, $this->idOf($carrier), $territoryType, $territoryId);
    }

    /**
     * @inheritdoc
     */
    public function update(
        CarrierTerritory $resource,
        $carrierId = null,
        $territoryType = null,
        Nullable $territoryId = null
    ) {
        $relationships = $this->filterNulls([
            CarrierTerritory::FIELD_ID_CARRIER     => $carrierId,
            CarrierTerritory::FIELD_TERRITORY_TYPE => $territoryType,
        ], [
            CarrierTerritory::FIELD_TERRITORY_ID   => $territoryId,
        ]);

        $this->updateWith($resource, [], $relationships);
    }

    /**
     * @param string $territoryType
     *
     * @return string
     */
    private function getTerritoryType($territoryType)
    {
        return S\arrayGetValueEx([
            Country::class => CarrierTerritory::TERRITORY_TYPE_COUNTRY,
            Region::class  => CarrierTerritory::TERRITORY_TYPE_REGION,
        ], $territoryType);
    }
}
