<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ImageRepository extends IndexBasedResourceRepository implements ImageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Image::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Image $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Image $resource, array $attributes = null)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
