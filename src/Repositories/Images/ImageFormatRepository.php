<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ImageFormatRepository extends BaseRepository implements ImageFormatRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImageFormat::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(ImageFormat $resource, array $attributes = [])
    {
        $this->updateWith($resource, $attributes, []);
    }
}
