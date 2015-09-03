<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CustomerTypeRepository extends BaseRepository implements CustomerTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CustomerType::class);
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
    public function update(CustomerType $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
