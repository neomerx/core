<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CarrierPostcode;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierPostcodeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier    $carrier
     * @param array|null $attributes
     *
     * @return CarrierPostcode
     */
    public function createWithObjects(Carrier $carrier, array $attributes = []);

    /**
     * @param int        $carrierId
     * @param array|null $attributes
     *
     * @return CarrierPostcode
     */
    public function create($carrierId, array $attributes = []);

    /**
     * @param CarrierPostcode $resource
     * @param Carrier|null    $carrier
     * @param array|null      $attributes
     *
     * @return void
     */
    public function updateWithObjects(CarrierPostcode $resource, Carrier $carrier = null, array $attributes = []);

    /**
     * @param CarrierPostcode $resource
     * @param int|null        $carrierId
     * @param array|null      $attributes
     *
     * @return void
     */
    public function update(CarrierPostcode $resource, $carrierId = null, array $attributes = []);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierPostcode
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
