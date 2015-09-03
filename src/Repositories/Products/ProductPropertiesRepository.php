<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductPropertiesRepository extends BaseRepository implements ProductPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Product $resource, Language $language, array $attributes)
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
        ProductProperties $properties,
        Product $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ProductProperties $properties,
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
            ProductProperties::FIELD_ID_PRODUCT  => $resourceId,
            ProductProperties::FIELD_ID_LANGUAGE => $languageId,
        ]);
    }
}
