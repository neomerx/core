<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\CountryProperties;
use \Illuminate\Database\Eloquent\Collection;

interface CountriesInterface extends CrudInterface
{
    const PARAM_CODE                   = Country::FIELD_CODE;
    const PARAM_PROPERTIES             = Country::FIELD_PROPERTIES;
    const PARAM_PROPERTIES_NAME        = CountryProperties::FIELD_NAME;
    const PARAM_PROPERTIES_DESCRIPTION = CountryProperties::FIELD_DESCRIPTION;

    /**
     * Create country.
     *
     * @param array $input
     *
     * @return Country
     */
    public function create(array $input);

    /**
     * Read country by identifier.
     *
     * @param string $code
     *
     * @return Country
     */
    public function read($code);

    /**
     * Get all countries in the system.
     *
     * @return Collection
     */
    public function all();

    /**
     * Get country regions.
     *
     * @param Country $country
     *
     * @return Collection
     */
    public function regions(Country $country);
}
