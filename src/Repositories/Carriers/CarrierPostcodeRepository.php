<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CarrierPostcode;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CarrierPostcodeRepository extends BaseRepository implements CarrierPostcodeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierPostcode::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Carrier $carrier, array $attributes = [])
    {
        return $this->create($this->idOf($carrier), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($carrierId, array $attributes = [])
    {
        $resource = $this->createWith($attributes, $this->getRelationships($carrierId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(CarrierPostcode $resource, Carrier $carrier = null, array $attributes = [])
    {
        $this->update($resource, $this->idOf($carrier), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(CarrierPostcode $resource, $carrierId = null, array $attributes = [])
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($carrierId));
    }

    /**
     * @param int $carrierId
     *
     * @return array
     */
    protected function getRelationships($carrierId)
    {
        return $this->filterNulls([
            CarrierPostcode::FIELD_ID_CARRIER => $carrierId,
        ]);
    }
}
