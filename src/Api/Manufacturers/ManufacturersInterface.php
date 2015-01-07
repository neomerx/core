<?php namespace Neomerx\Core\Api\Manufacturers;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\Manufacturer;
use \Illuminate\Database\Eloquent\Collection;

interface ManufacturersInterface extends CrudInterface
{
    const PARAM_ADDRESS    = Manufacturer::FIELD_ADDRESS;
    const PARAM_PROPERTIES = Manufacturer::FIELD_PROPERTIES;

    /**
     * Create manufacturer.
     *
     * @param array $input
     *
     * @return Manufacturer
     */
    public function create(array $input);

    /**
     * Read manufacturer by identifier.
     *
     * @param string $code
     *
     * @return Manufacturer
     */
    public function read($code);

    /**
     * Search manufacturers.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
