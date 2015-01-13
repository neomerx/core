<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ImageProperties;

interface ImagePropertiesRepositoryInterface
{
    /**
     * @param Image      $image
     * @param Language   $language
     * @param array|null $attributes
     *
     * @return ImageProperties
     */
    public function instance(Image $image, Language $language, array $attributes = null);

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
}
