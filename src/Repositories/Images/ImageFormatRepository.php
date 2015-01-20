<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class ImageFormatRepository extends CodeBasedResourceRepository implements ImageFormatRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImageFormat::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var ImageFormat $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ImageFormat $resource, array $attributes = null)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
