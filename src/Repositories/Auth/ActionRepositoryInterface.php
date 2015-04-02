<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Action;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ActionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Action
     */
    public function instance(array $attributes);

    /**
     * @param Action $resource
     * @param array  $attributes
     *
     * @return void
     */
    public function fill(Action $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Action
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
