<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Models\CarrierTerritory;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CarrierTerritoryRepository extends IndexBasedResourceRepository implements CarrierTerritoryRepositoryInterface
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
    public function instance(Carrier $carrier, BaseModel $territory)
    {
        /** @var CarrierTerritory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $carrier, $territory);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(CarrierTerritory $resource, Carrier $carrier = null, BaseModel $territory = null)
    {
        if ($territory !== null) {
            $resource->{CarrierTerritory::FIELD_TERRITORY_ID}   = $territory->getKey();
            $resource->{CarrierTerritory::FIELD_TERRITORY_TYPE} = S\arrayGetValueEx([
                Country::class => CarrierTerritory::TERRITORY_TYPE_COUNTRY,
                Region::class  => CarrierTerritory::TERRITORY_TYPE_REGION,
            ], get_class($territory));
        }

        $this->fillModel($resource, [
            CarrierTerritory::FIELD_ID_CARRIER => $carrier,
        ]);
    }

    /**
     * @param Carrier $carrier
     *
     * @return CarrierTerritory
     *
     */
    public function instanceAllCountries(Carrier $carrier)
    {
        /** @var CarrierTerritory $resource */
        $resource = $this->makeModel();
        $this->fillAllCountries($resource, $carrier);
        return $resource;
    }

    /**
     * @param Carrier $carrier
     *
     * @return CarrierTerritory
     *
     */
    public function instanceAllRegions(Carrier $carrier)
    {
        /** @var CarrierTerritory $resource */
        $resource = $this->makeModel();
        $this->fillAllRegions($resource, $carrier);
        return $resource;
    }

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     *
     * @return void
     */
    public function fillAllCountries(CarrierTerritory $resource, Carrier $carrier = null)
    {
        $this->fillTerritory($resource, CarrierTerritory::TERRITORY_TYPE_COUNTRY);
        $this->fillModel($resource, [CarrierTerritory::FIELD_ID_CARRIER => $carrier]);
    }

    /**
     * @param CarrierTerritory $resource
     * @param Carrier|null     $carrier
     *
     * @return void
     */
    public function fillAllRegions(CarrierTerritory $resource, Carrier $carrier = null)
    {
        $this->fillTerritory($resource, CarrierTerritory::TERRITORY_TYPE_REGION);
        $this->fillModel($resource, [CarrierTerritory::FIELD_ID_CARRIER => $carrier]);
    }

    /**
     * @param CarrierTerritory $resource
     * @param string           $type
     */
    private function fillTerritory(CarrierTerritory $resource, $type)
    {
        if ($resource->exists === true) {
            $resource->{CarrierTerritory::FIELD_TERRITORY_ID} = null;
        } else {
            unset($resource[CarrierTerritory::FIELD_TERRITORY_ID]);
        }
        $resource->{CarrierTerritory::FIELD_TERRITORY_TYPE} = $type;
    }
}
