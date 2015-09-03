<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Region::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Country $country, array $attributes)
    {
        return $this->create($this->idOf($country), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($countryId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($countryId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Region $resource, Country $country = null, array $attributes = null)
    {
        $this->update($resource, $this->idOf($country), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(Region $resource, $countryId = null, array $attributes = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($countryId));
    }

    /**
     * @param int $countryId
     *
     * @return array
     */
    protected function getRelationships($countryId)
    {
        return $this->filterNulls([
            Region::FIELD_ID_COUNTRY => $countryId,
        ]);
    }
}
