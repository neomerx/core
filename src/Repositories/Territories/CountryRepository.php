<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class CountryRepository extends CodeBasedResourceRepository implements CountryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Country::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Country $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Country $country, array $attributes = null)
    {
        $this->fillModel($country, [], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function regions(Country $resource)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $resource->regions()->orderBy(Region::FIELD_POSITION, 'asc')->get();
    }
}
