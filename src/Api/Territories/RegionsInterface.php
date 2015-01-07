<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface RegionsInterface extends CrudInterface
{
    const PARAM_CODE         = Region::FIELD_CODE;
    const PARAM_NAME         = Region::FIELD_NAME;
    const PARAM_POSITION     = Region::FIELD_POSITION;
    const PARAM_COUNTRY_CODE = 'country_code';

    /**
     * Create region.
     *
     * @param array $input
     *
     * @return Region
     */
    public function create(array $input);

    /**
     * Read region by identifier.
     *
     * @param string $code
     *
     * @return Region
     */
    public function read($code);

    /**
     * Search regions.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
