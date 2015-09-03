<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Image::class);
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
    public function update(Image $resource, array $attributes = [])
    {
        $this->updateWith($resource, $attributes, []);
    }
}
