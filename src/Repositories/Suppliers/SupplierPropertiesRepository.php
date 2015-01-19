<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\SupplierProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Suppliers\SupplierPropertiesRepositoryInterface as PropertiesRepositoryInterface;

class SupplierPropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplierProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Supplier $resource, Language $language, array $attributes)
    {
        /** @var SupplierProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        SupplierProperties $properties,
        Supplier $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            SupplierProperties::FIELD_ID_SUPPLIER => $resource,
            SupplierProperties::FIELD_ID_LANGUAGE => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
