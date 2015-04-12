<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CustomerRiskRepository extends CodeBasedResourceRepository implements CustomerRiskRepositoryInterface
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
    public function instance(array $attributes)
    {
        /** @var CustomerRisk $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(CustomerRisk $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
