<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CountryProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CountryPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Country  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CountryProperty
     */
    public function createWithObjects(Country $resource, Language $language, array $attributes);

    /**
     * @param int   $countryId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CountryProperty
     */
    public function create($countryId, $languageId, array $attributes);

    /**
     * @param CountryProperty $properties
     * @param Country|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CountryProperty $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CountryProperty $properties
     * @param int|null          $countryId
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        CountryProperty $properties,
        $countryId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CountryProperty
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
