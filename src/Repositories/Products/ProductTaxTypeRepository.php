<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductTaxTypeRepository extends BaseRepository implements ProductTaxTypeRepositoryInterface
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
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(ProductTaxType $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
