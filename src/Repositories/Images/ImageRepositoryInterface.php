<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Image
     */
    public function instance(array $attributes);

    /**
     * @param Image $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Image $resource, array $attributes = null);

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Image
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
