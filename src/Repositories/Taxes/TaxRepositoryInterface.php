<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Tax
     */
    public function instance(array $attributes);

    /**
     * @param Tax   $resource
     * @param array $attributes
     *
     * @return void
     */
    public function fill(Tax $resource, array $attributes);

    /**
     * @param string $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Tax
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Tax
     */
    public function readByCode($code, array $scopes = [], array $columns = ['*']);

    /**
     * Select taxes.
     *
     * @param int|null   $countryId
     * @param int|null   $regionId
     * @param int|string $postcode
     * @param int|null   $customerTypeId
     * @param int|null   $productTaxTypeId
     *
     * @return Collection
     */
    public function selectTaxes(
        $countryId,
        $regionId,
        $postcode,
        $customerTypeId,
        $productTaxTypeId
    );
}
