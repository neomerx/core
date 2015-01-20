<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ImagePathRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Image       $image
     * @param ImageFormat $format
     * @param array       $attributes
     *
     * @return ImagePath
     */
    public function instance(Image $image, ImageFormat $format, array $attributes);

    /**
     * @param ImagePath   $resource
     * @param Image       $image
     * @param ImageFormat $format
     * @param array       $attributes
     *
     * @return void
     */
    public function fill(
        ImagePath $resource,
        Image $image = null,
        ImageFormat $format = null,
        array $attributes = null
    );

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ImagePath
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
