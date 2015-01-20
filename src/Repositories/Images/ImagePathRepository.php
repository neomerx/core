<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ImagePathRepository extends IndexBasedResourceRepository implements ImagePathRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImagePath::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Image $image, ImageFormat $format, array $attributes)
    {
        /** @var ImagePath $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $image, $format, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ImagePath $resource, Image $image = null, ImageFormat $format = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            ImagePath::FIELD_ID_IMAGE        => $image,
            ImagePath::FIELD_ID_IMAGE_FORMAT => $format,
        ], $attributes);
    }
}
