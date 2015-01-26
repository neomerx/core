<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\CarrierCustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CarrierCustomerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier           $carrier
     * @param CustomerType|null $type
     *
     * @return CarrierCustomerType
     *
     */
    public function instance(Carrier $carrier, CustomerType $type = null);

    /**
     * @param CarrierCustomerType $resource
     * @param Carrier|null        $carrier
     * @param CustomerType|null   $type
     *
     * @return void
     */
    public function fill(CarrierCustomerType $resource, Carrier $carrier = null, CustomerType $type = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CarrierCustomerType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
