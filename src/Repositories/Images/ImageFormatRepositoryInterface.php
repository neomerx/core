<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ImageFormatRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return ImageFormat
     */
    public function create(array $attributes);

    /**
     * @param ImageFormat $resource
     * @param array|null  $attributes
     *
     * @return void
     */
    public function update(ImageFormat $resource, array $attributes = []);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ImageFormat
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
