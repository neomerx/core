<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class RegionRepository extends CodeBasedResourceRepository implements RegionRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Region::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Country $country, array $attributes)
    {
        /** @var Region $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $country, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Region $resource, Country $country = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            Region::FIELD_ID_COUNTRY => $country,
        ], $attributes);
    }
}
