<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class CustomerTypeRepository extends CodeBasedResourceRepository implements CustomerTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CustomerType::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var CustomerType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(CustomerType $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}