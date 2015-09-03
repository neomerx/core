<?php namespace Neomerx\Core\Repositories\Images;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ImagePathRepository extends BaseRepository implements ImagePathRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ImagePath::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Image $image, ImageFormat $format, array $attributes)
    {
        return $this->create($this->idOf($image), $this->idOf($format), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($imageId, $formatId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($imageId, $formatId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        ImagePath $resource,
        Image $image = null,
        ImageFormat $format = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($image), $this->idOf($format), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ImagePath $resource,
        $imageId = null,
        $formatId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($imageId, $formatId));
    }

    /**
     * @param int $imageId
     * @param int $formatId
     *
     * @return array
     */
    protected function getRelationships($imageId, $formatId)
    {
        return $this->filterNulls([
            ImagePath::FIELD_ID_IMAGE        => $imageId,
            ImagePath::FIELD_ID_IMAGE_FORMAT => $formatId,
        ]);
    }
}
