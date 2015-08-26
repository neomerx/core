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
     * @param array    $attributes
     * @param Currency $currency
     *
     * @return Carrier
     */
    public function instance(array $attributes, Currency $currency);

    /**
     * @param Carrier       $resource
     * @param array|null    $attributes
     * @param Currency|null $currency
     *
     * @return void
     */
    public function fill(Carrier $resource, array $attributes, Currency $currency = null);

    /**
     * @param string $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Carrier
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Carrier
     */
    public function readByCode($code, array $scopes = [], array $columns = ['*']);

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
