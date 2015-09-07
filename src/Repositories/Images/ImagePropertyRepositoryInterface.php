<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ImageProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ImagePropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Image    $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return ImageProperty
     */
    public function createWithObjects(Image $resource, Language $language, array $attributes);

    /**
     * @param int   $imageId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return ImageProperty
     */
    public function create($imageId, $languageId, array $attributes);

    /**
     * @param ImageProperty $properties
     * @param Image|null      $resource
     * @param Language|null   $language
     * @param array|null      $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ImageProperty $properties,
        Image $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param ImageProperty $properties
     * @param int|null        $imageId
     * @param int|null        $languageId
     * @param array|null      $attributes
     *
     * @return void
     */
    public function update(
        ImageProperty $properties,
        $imageId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return ImageProperty
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
