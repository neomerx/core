<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CustomerRiskRepository extends BaseRepository implements CustomerRiskRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CustomerRisk::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(CustomerRisk $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
