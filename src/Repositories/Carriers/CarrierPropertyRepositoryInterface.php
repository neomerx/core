<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CarrierProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CarrierProperty
     */
    public function createWithObjects(Carrier $resource, Language $language, array $attributes);

    /**
     * @param int   $carrierId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CarrierProperty
     */
    public function create($carrierId, $languageId, array $attributes);

    /**
     * @param CarrierProperty $properties
     * @param Carrier|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CarrierProperty $properties,
        Carrier $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CarrierProperty $properties
     * @param int|null          $carrierId
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        CarrierProperty $properties,
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
