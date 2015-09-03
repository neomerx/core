<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CarrierTerritory;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierTerritoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier   $carrier
     * @param BaseModel $territory
     *
     * @return CarrierTerritory
     */
    public function createWithObjects(Carrier $carrier, BaseModel $territory);

    /**
     * @param int      $carrierId
     * @param string   $territoryType Class of the territory (country or region)
     * @param int|null $territoryId
     *
     * @return CarrierTerritory
     */
    public function create($carrierId, $territoryType, $territoryId);

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     * @param BaseModel|null   $territory
     *
     * @return void
     */
    public function updateWithObjects(CarrierTerritory $resource, Carrier $carrier = null, BaseModel $territory = null);

    /**
     * @param CarrierTerritory $resource
     * @param int|null         $carrierId
     * @param string|null      $territoryType
     * @param Nullable|null    $territoryId
     *
     * @return void
     */
    public function update(
        CarrierTerritory $resource,
        $carrierId = null,
        $territoryType = null,
        Nullable $territoryId = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierTerritory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
