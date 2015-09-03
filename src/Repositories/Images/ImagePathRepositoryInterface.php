<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ImagePathRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Image       $image
     * @param ImageFormat $format
     * @param array       $attributes
     *
     * @return ImagePath
     */
    public function createWithObjects(Image $image, ImageFormat $format, array $attributes);

    /**
     * @param int   $imageId
     * @param int   $formatId
     * @param array $attributes
     *
     * @return ImagePath
     */
    public function create($imageId, $formatId, array $attributes);

    /**
     * @param ImagePath        $resource
     * @param Image|null       $image
     * @param ImageFormat|null $format
     * @param array|null       $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ImagePath $resource,
        Image $image = null,
        ImageFormat $format = null,
        array $attributes = null
    );

    /**
     * @param ImagePath  $resource
     * @param int|null   $imageId
     * @param int|null   $formatId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(
        ImagePath $resource,
        $imageId = null,
        $formatId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return ImagePath
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
