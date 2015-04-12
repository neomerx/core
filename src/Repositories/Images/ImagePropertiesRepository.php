<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ImageProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ImagePropertiesRepository extends IndexBasedResourceRepository implements ImagePropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImageProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Image $image, Language $language, array $attributes)
    {
        /** @var ImageProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $image, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ImageProperties $properties,
        Image $image = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [ImageProperties::FIELD_ID_IMAGE => $image, ImageProperties::FIELD_ID_LANGUAGE => $language];
        $this->fillModel($properties, $data, $attributes);
    }
}
