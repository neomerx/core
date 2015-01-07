<?php namespace Neomerx\Core\Api\Images;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\ImageFormat;
use \Illuminate\Database\Eloquent\Collection;

interface ImageFormatsInterface extends CrudInterface
{
    /**
     * Create image format.
     *
     * @param array $input
     *
     * @return ImageFormat
     */
    public function create(array $input);

    /**
     * Read image format by identifier.
     *
     * @param string $code
     *
     * @return ImageFormat
     */
    public function read($code);

    /**
     * Get all image formats in the system.
     *
     * @return Collection
     */
    public function all();
}
