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
    public function instance(Country $resource, Language $language, array $attributes);

    /**
     * @param CountryProperties $properties
     * @param Country|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function fill(
        CountryProperties $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    );
}
