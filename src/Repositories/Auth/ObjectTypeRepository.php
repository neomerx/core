<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ObjectTypeRepository extends BaseRepository implements ObjectTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ObjectType::class);
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
    public function update(ObjectType $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
