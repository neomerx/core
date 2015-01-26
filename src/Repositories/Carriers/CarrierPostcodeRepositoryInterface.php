<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CarrierPostcode;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CarrierPostcodeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier    $carrier
     * @param array|null $attributes
     *
     * @return CarrierPostcode
     */
    public function instance(Carrier $carrier, array $attributes = null);

    /**
     * @param CarrierPostcode $resource
     * @param Carrier|null    $carrier
     * @param array|null      $attributes
     *
     * @return void
     */
    public function fill(CarrierPostcode $resource, Carrier $carrier = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierPostcode
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
