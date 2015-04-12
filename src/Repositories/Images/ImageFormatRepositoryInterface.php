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
    public function instance(array $attributes);

    /**
     * @param ImageFormat $resource
     * @param array|null  $attributes
     *
     * @return void
     */
    public function fill(ImageFormat $resource, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ImageFormat
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
