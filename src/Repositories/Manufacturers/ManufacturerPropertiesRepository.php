<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Models\ManufacturerProperties;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerPropertiesRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class ManufacturerPropertiesRepository extends BaseRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ManufacturerProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Manufacturer $resource, Language $language, array $attributes)
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
        ManufacturerProperties $properties,
        Manufacturer $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ManufacturerProperties $properties,
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
            ManufacturerProperties::FIELD_ID_MANUFACTURER => $resourceId,
            ManufacturerProperties::FIELD_ID_LANGUAGE     => $languageId,
        ]);
    }
}
