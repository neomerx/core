<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CountryProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CountryPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Country  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CountryProperties
     */
    public function createWithObjects(Country $resource, Language $language, array $attributes);

    /**
     * @param int   $countryId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CountryProperties
     */
    public function create($countryId, $languageId, array $attributes);

    /**
     * @param CountryProperties $properties
     * @param Country|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CountryProperties $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CountryProperties $properties
     * @param int|null          $countryId
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        CountryProperties $properties,
        $countryId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CountryProperties
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
