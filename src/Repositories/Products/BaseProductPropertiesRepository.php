<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\BaseProductProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Products\BaseProductPropertiesRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class BaseProductPropertiesRepository extends IndexBasedResourceRepository implements RepositoryInterface
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
    public function instance(BaseProduct $resource, Language $language, array $attributes)
    {
        /** @var BaseProductProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        BaseProductProperties $properties,
        BaseProduct $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            BaseProductProperties::FIELD_ID_BASE_PRODUCT => $resource,
            BaseProductProperties::FIELD_ID_LANGUAGE     => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
