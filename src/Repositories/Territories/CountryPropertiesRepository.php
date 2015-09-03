<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CountryProperties;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CountryPropertiesRepository extends BaseRepository implements CountryPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CountryProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Country $resource, Language $language, array $attributes)
    {
        return $this->create($this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($resourceId, $languageId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($resourceId, $languageId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        CountryProperties $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        CountryProperties $properties,
        $resourceId = null,
        $languageId = null,
        array $attributes = null
    ) {
        $this->updateWith($properties, $attributes, $this->getRelationships($resourceId, $languageId));
    }

    /**
     * @param int $resourceId
     * @param int $languageId
     *
     * @return array
     */
    protected function getRelationships($resourceId, $languageId)
    {
        return $this->filterNulls([
            CountryProperties::FIELD_ID_COUNTRY  => $resourceId,
            CountryProperties::FIELD_ID_LANGUAGE => $languageId,
        ]);
    }
}
