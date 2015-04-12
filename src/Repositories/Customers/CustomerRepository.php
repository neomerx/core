<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CustomerRepository extends IndexBasedResourceRepository implements CustomerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Customer::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(CustomerType $type, Language $language, array $attributes, CustomerRisk $risk = null)
    {
        /** @var Customer $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $type, $language, $attributes, $risk);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Customer $resource,
        CustomerType $type = null,
        Language $language = null,
        array $attributes = null,
        CustomerRisk $risk = null
    ) {
        $this->fillModel($resource, [
            Customer::FIELD_ID_CUSTOMER_RISK => $risk,
            Customer::FIELD_ID_CUSTOMER_TYPE => $type,
            Customer::FIELD_ID_LANGUAGE      => $language,
        ], $attributes);
    }
}
