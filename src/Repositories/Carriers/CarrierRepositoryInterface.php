<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Support\Nullable;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array    $attributes
     * @param Nullable|null $currency Currency
     *
     * @return Carrier
     */
    public function createWithObjects(array $attributes, Nullable $currency = null);

    /**
     * @param array    $attributes
     * @param Nullable|null $currencyId
     *
     * @return Carrier
     */
    public function create(array $attributes, Nullable $currencyId = null);

    /**
     * @param Carrier       $resource
     * @param array|null    $attributes
     * @param Nullable|null $currency Currency
     *
     * @return void
     */
    public function updateWithObjects(Carrier $resource, array $attributes, Nullable $currency = null);

    /**
     * @param Carrier       $resource
     * @param array|null    $attributes
     * @param Nullable|null $currencyId
     *
     * @return void
     */
    public function update(Carrier $resource, array $attributes, Nullable $currencyId = null);

    /**
     * @param int   $index
     * @param array $scopes
     * @param array $columns
     *
     * @return Carrier
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

    /**
     * @param int        $countryId
     * @param int        $regionId
     * @param mixed      $postcode
     * @param int        $customerTypeId
     * @param float|null $pkgWeight
     * @param float|null $maxDimension
     * @param float|null $pkgCost
     *
     * @return Collection
     */
    public function selectCarriers(
        $countryId,
        $regionId,
        $postcode,
        $customerTypeId,
        $pkgWeight = null,
        $maxDimension = null,
        $pkgCost = null
    );
}
