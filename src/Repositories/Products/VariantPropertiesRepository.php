<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\VariantProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class VariantPropertiesRepository extends IndexBasedResourceRepository implements VariantPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(VariantProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Variant $resource, Language $language, array $attributes)
    {
        /** @var VariantProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        VariantProperties $properties,
        Variant $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [VariantProperties::FIELD_ID_VARIANT => $resource, VariantProperties::FIELD_ID_LANGUAGE => $language];
        $this->fillModel($properties, $data, $attributes);
    }
}
