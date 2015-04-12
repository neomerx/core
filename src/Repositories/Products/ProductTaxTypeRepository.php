<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ProductTaxTypeRepository extends CodeBasedResourceRepository implements ProductTaxTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductTaxType::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var ProductTaxType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ProductTaxType $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
