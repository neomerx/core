<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface BaseProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Manufacturer $manufacturer
     * @param array        $attributes
     *
     * @return BaseProduct
     */
    public function instance(
        Manufacturer $manufacturer,
        array $attributes
    );

    /**
     * @param BaseProduct       $base
     * @param Manufacturer|null $manufacturer
     * @param array|null        $attributes
     *
     * @return void
     */
    public function fill(
        BaseProduct $base,
        Manufacturer $manufacturer = null,
        array $attributes = null
    );

    /**
     * @param string $sku
     * @param array  $relations
     * @param array  $columns
     *
     * @return BaseProduct
     */
    public function read($sku, array $relations = [], array $columns = ['*']);
}
