<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Country::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Country $country, array $attributes = null)
    {
        $this->updateWith($country, $attributes, []);
    }
}
