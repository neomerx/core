<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CarrierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Carrier
     */
    public function instance(array $attributes);

    /**
     * @param Carrier    $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Carrier $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Carrier
     */
    public function read($code, array $scopes = [], array $columns = ['*']);

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
