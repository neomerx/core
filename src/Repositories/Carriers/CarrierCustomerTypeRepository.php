<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\CarrierCustomerType;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Carriers\CarrierCustomerTypeRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class CarrierCustomerTypeRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierCustomerType::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Carrier $carrier, Nullable $type = null)
    {
        return $this->create($this->idOf($carrier), $this->idOfNullable($type, CustomerType::class));
    }

    /**
     * @inheritdoc
     */
    public function create($carrierId, Nullable $typeId = null)
    {
        $resource = $this->createWith([], $this->getRelationships($carrierId, $typeId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        CarrierCustomerType $resource,
        Carrier $carrier = null,
        Nullable $type = null
    ) {
        $this->update($resource, $this->idOf($carrier), $this->idOfNullable($type, CustomerType::class));
    }

    /**
     * @inheritdoc
     */
    public function update(
        CarrierCustomerType $resource,
        $carrierId = null,
        Nullable $typeId = null
    ) {
        $this->updateWith($resource, [], $this->getRelationships($carrierId, $typeId));
    }

    /**
     * @param int           $carrierId
     * @param Nullable|null $typeId
     *
     * @return array
     */
    protected function getRelationships($carrierId, Nullable $typeId = null)
    {
        return $this->filterNulls([
            CarrierCustomerType::FIELD_ID_CARRIER => $carrierId,
        ], [
            CarrierCustomerType::FIELD_ID_CUSTOMER_TYPE => $typeId,
        ]);
    }
}
