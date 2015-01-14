<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Country;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
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
    public function instance(array $attributes = null)
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
    public function read($code, array $scopes = [], array $columns = ['*'])
    {
        return $this->findModelByCode($code, $scopes, $columns);
    }
}
