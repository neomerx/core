<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CarrierCustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierCustomerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier       $carrier
     * @param Nullable|null $type CustomerType
     *
     * @return CarrierCustomerType
     */
    public function createWithObjects(Carrier $carrier, Nullable $type = null);

    /**
     * @param int           $carrierId
     * @param Nullable|null $typeId
     *
     * @return CarrierCustomerType
     */
    public function create($carrierId, Nullable $typeId = null);

    /**
     * @param CarrierCustomerType $resource
     * @param Carrier|null        $carrier
     * @param Nullable|null       $type CustomerType
     *
     * @return void
     */
    public function updateWithObjects(
        CarrierCustomerType $resource,
        Carrier $carrier = null,
        Nullable $type = null
    );

    /**
     * @param CarrierCustomerType $resource
     * @param int|null            $carrierId
     * @param Nullable|null       $typeId
     *
     * @return void
     */
    public function update(
        CarrierCustomerType $resource,
        $carrierId = null,
        Nullable $typeId = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierCustomerType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
