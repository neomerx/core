<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\BaseProductProperties;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Products\BaseProductPropertiesRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class BaseProductPropertiesRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(BaseProductProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(BaseProduct $resource, Language $language, array $attributes)
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
        BaseProductProperties $properties,
        BaseProduct $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        BaseProductProperties $properties,
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
            BaseProductProperties::FIELD_ID_BASE_PRODUCT => $resourceId,
            BaseProductProperties::FIELD_ID_LANGUAGE     => $languageId,
        ]);
    }
}
