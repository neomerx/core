<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CarrierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Carrier
     */
    public function instance(array $attributes);

    /**
     * @param Carrier    $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Carrier $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Carrier
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
