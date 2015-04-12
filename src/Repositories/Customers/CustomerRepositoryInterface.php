<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CustomerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CustomerType      $type
     * @param Language          $language
     * @param array             $attributes
     * @param CustomerRisk|null $risk
     *
     * @return Customer
     */
    public function instance(CustomerType $type, Language $language, array $attributes, CustomerRisk $risk = null);

    /**
     * @param Customer          $resource
     * @param CustomerType|null $type
     * @param Language|null     $language
     * @param array|null        $attributes
     * @param CustomerRisk|null $risk
     *
     * @return void
     */
    public function fill(
        Customer $resource,
        CustomerType $type = null,
        Language $language = null,
        array $attributes = null,
        CustomerRisk $risk = null
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
