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
    public function create(array $attributes);

    /**
     * @param Tax   $resource
     * @param array $attributes
     *
     * @return void
     */
    public function update(Tax $resource, array $attributes);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Tax
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

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
