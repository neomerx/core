<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Image
     */
    public function create(array $attributes);

    /**
     * @param Image      $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Image $resource, array $attributes = []);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Image
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
