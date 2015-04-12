<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CurrencyProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CurrencyPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Currency $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CurrencyProperties
     */
    public function instance(Currency $resource, Language $language, array $attributes);

    /**
     * @param CurrencyProperties $properties
     * @param Currency|null $resource
     * @param Language|null $language
     * @param array|null    $attributes
     *
     * @return void
     */
    public function fill(
        CurrencyProperties $properties,
        Currency $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Currency
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
