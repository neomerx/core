<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ProductTaxTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return ProductTaxType
     */
    public function instance(array $attributes);

    /**
     * @param ProductTaxType $resource
     * @param array          $attributes
     *
     * @return void
     */
    public function fill(ProductTaxType $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ProductTaxType
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
