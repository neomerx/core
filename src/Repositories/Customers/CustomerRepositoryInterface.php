<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CustomerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CustomerType  $type
     * @param Language      $language
     * @param array         $attributes
     * @param Nullable|null $risk CustomerRisk
     *
     * @return Customer
     */
    public function createWithObjects(CustomerType $type, Language $language, array $attributes, Nullable $risk = null);

    /**
     * @param int           $typeId
     * @param int           $languageId
     * @param array         $attributes
     * @param Nullable|null $riskId
     *
     * @return Customer
     */
    public function create($typeId, $languageId, array $attributes, Nullable $riskId = null);

    /**
     * @param Customer          $resource
     * @param CustomerType|null $type
     * @param Language|null     $language
     * @param array|null        $attributes
     * @param Nullable|null     $risk CustomerRisk
     *
     * @return void
     */
    public function updateWithObjects(
        Customer $resource,
        CustomerType $type = null,
        Language $language = null,
        array $attributes = null,
        Nullable $risk = null
    );

    /**
     * @param Customer      $resource
     * @param int|null      $typeId
     * @param int|null      $languageId
     * @param array|null    $attributes
     * @param Nullable|null $riskId
     *
     * @return void
     */
    public function update(
        Customer $resource,
        $typeId = null,
        $languageId = null,
        array $attributes = null,
        Nullable $riskId = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Customer
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
