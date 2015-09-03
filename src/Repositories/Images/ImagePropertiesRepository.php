<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ImageProperties;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ImagePropertiesRepository extends BaseRepository implements ImagePropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImageProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Image $resource, Language $language, array $attributes)
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
        ImageProperties $properties,
        Image $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ImageProperties $properties,
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
            ImageProperties::FIELD_ID_IMAGE    => $resourceId,
            ImageProperties::FIELD_ID_LANGUAGE => $languageId,
        ]);
    }
}
