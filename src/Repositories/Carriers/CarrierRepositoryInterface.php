<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Currency;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Currency $currency
     * @param array    $attributes
     *
     * @return Carrier
     */
    public function createWithObjects(Currency $currency, array $attributes);

    /**
     * @param int   $currencyId
     * @param array $attributes
     *
     * @return Carrier
     */
    public function create($currencyId, array $attributes);

    /**
     * @param Carrier       $resource
     * @param Currency|null $currency
     * @param array|null    $attributes
     *
     * @return void
     */
    public function updateWithObjects(Carrier $resource, Currency $currency = null, array $attributes = null);

    /**
     * @param Carrier    $resource
     * @param int|null   $currencyId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Carrier $resource, $currencyId = null, array $attributes = []);

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
