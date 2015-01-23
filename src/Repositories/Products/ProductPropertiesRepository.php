<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ProductPropertiesRepository extends IndexBasedResourceRepository implements ProductPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $resource, Language $language, array $attributes)
    {
        /** @var ProductProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ProductProperties $properties,
        Product $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [ProductProperties::FIELD_ID_PRODUCT => $resource, ProductProperties::FIELD_ID_LANGUAGE => $language];
        $this->fillModel($properties, $data, $attributes);
    }
}
