<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\SupplierProperty;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Suppliers\SupplierPropertyRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class SupplierPropertyRepository extends BaseRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplierProperty::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Supplier $resource, Language $language, array $attributes)
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
        SupplierProperty $properties,
        Supplier $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        SupplierProperty $properties,
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
            SupplierProperty::FIELD_ID_SUPPLIER => $resourceId,
            SupplierProperty::FIELD_ID_LANGUAGE => $languageId,
        ]);
    }
}
