<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Models\CarrierTerritory;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CarrierTerritoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier   $carrier
     * @param BaseModel $territory
     *
     * @return CarrierTerritory
     *
     */
    public function instance(Carrier $carrier, BaseModel $territory);

    /**
     * @param Carrier $carrier
     *
     * @return CarrierTerritory
     *
     */
    public function instanceAllCountries(Carrier $carrier);

    /**
     * @param Carrier $carrier
     *
     * @return CarrierTerritory
     *
     */
    public function instanceAllRegions(Carrier $carrier);

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     * @param BaseModel|null   $territory
     *
     * @return void
     */
    public function fill(CarrierTerritory $resource, Carrier $carrier = null, BaseModel $territory = null);

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     *
     * @return void
     */
    public function fillAllCountries(CarrierTerritory $resource, Carrier $carrier = null);

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     *
     * @return void
     */
    public function fillAllRegions(CarrierTerritory $resource, Carrier $carrier = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierTerritory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
