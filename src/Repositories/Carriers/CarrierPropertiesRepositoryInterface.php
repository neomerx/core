<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CarrierProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CarrierProperties
     */
    public function createWithObjects(Carrier $resource, Language $language, array $attributes);

    /**
     * @param int   $carrierId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CarrierProperties
     */
    public function create($carrierId, $languageId, array $attributes);

    /**
     * @param CarrierProperties $properties
     * @param Carrier|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CarrierProperties $properties,
        Carrier $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CarrierProperties $properties
     * @param int|null          $carrierId
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        CarrierProperties $properties,
        $carrierId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Carrier
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
