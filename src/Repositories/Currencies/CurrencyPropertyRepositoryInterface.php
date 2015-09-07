<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CurrencyProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CurrencyPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Currency $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CurrencyProperty
     */
    public function createWithObjects(Currency $resource, Language $language, array $attributes);

    /**
     * @param int   $currencyId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CurrencyProperty
     */
    public function create($currencyId, $languageId, array $attributes);

    /**
     * @param CurrencyProperty $properties
     * @param Currency|null      $resource
     * @param Language|null      $language
     * @param array|null         $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CurrencyProperty $properties,
        Currency $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CurrencyProperty $properties
     * @param int|null           $currencyId
     * @param int|null           $languageId
     * @param array|null         $attributes
     *
     * @return void
     */
    public function update(
        CurrencyProperty $properties,
        $currencyId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CurrencyProperty
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
