<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ObjectTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return ObjectType
     */
    public function create(array $attributes);

    /**
     * @param ObjectType $resource
     * @param array  $attributes
     *
     * @return void
     */
    public function update(ObjectType $resource, array $attributes);

    /**
     * @param int   $idx
     * @param array $scopes
     * @param array $columns
     *
     * @return ObjectType
     */
    public function read($idx, array $scopes = [], array $columns = ['*']);
}
