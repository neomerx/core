<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ImageProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ImagePropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Image    $image
     * @param Language $language
     * @param array    $attributes
     *
     * @return ImageProperties
     */
    public function instance(Image $image, Language $language, array $attributes);

    /**
     * @param ImageProperties $properties
     * @param Image|null      $image
     * @param Language|null   $language
     * @param array|null      $attributes
     *
     * @return void
     */
    public function fill(
        ImageProperties $properties,
        Image $image = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return ImageProperties
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
