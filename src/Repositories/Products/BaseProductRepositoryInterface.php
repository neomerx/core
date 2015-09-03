<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Currency;
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
     * @param Currency     $currency
     * @param array        $attributes
     *
     * @return BaseProduct
     */
    public function createWithObjects(
        Manufacturer $manufacturer,
        Currency $currency,
        array $attributes
    );

   /**
    * @param int   $manufacturerId
    * @param int   $currencyId
    * @param array $attributes
     *
     * @return BaseProduct
     */
    public function create(
        $manufacturerId,
        $currencyId,
        array $attributes
    );

    /**
     * @param BaseProduct       $base
     * @param Manufacturer|null $manufacturer
     * @param Currency|null     $currency
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        BaseProduct $base,
        Manufacturer $manufacturer = null,
        Currency $currency = null,
        array $attributes = null
    );

    /**
     * @param BaseProduct $base
     * @param int|null    $manufacturerId
     * @param int|null    $currencyId
     * @param array|null  $attributes
     *
     * @return void
     */
    public function update(
        BaseProduct $base,
        $manufacturerId = null,
        $currencyId = null,
        array $attributes = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return BaseProduct
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
