<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface CustomersInterface extends CrudInterface, CustomerAddressesInterface
{
    const PARAM_RISK_CODE                = 'risk_code';
    const PARAM_TYPE_CODE                = 'type_code';
    const PARAM_LANGUAGE_CODE            = 'language_code';
    const PARAM_FIRST_NAME               = Customer::FIELD_FIRST_NAME;
    const PARAM_LAST_NAME                = Customer::FIELD_LAST_NAME;
    const PARAM_EMAIL                    = Customer::FIELD_EMAIL;
    const PARAM_MOBILE                   = Customer::FIELD_MOBILE;
    const PARAM_GENDER                   = Customer::FIELD_GENDER;
    const PARAM_DEFAULT_BILLING_ADDRESS  = Customer::FIELD_DEFAULT_BILLING_ADDRESS;
    const PARAM_DEFAULT_SHIPPING_ADDRESS = Customer::FIELD_DEFAULT_SHIPPING_ADDRESS;

    /**
     * Create customer.
     *
     * @param array $input
     *
     * @return Customer
     */
    public function create(array $input);

    /**
     * Read customer by identifier.
     *
     * @param int $customerId
     *
     * @return Customer
     */
    public function read($customerId);
    /**
     * Update customer.
     *
     * @param int   $customerId
     * @param array $input
     *
     * @return void
     */
    public function update($customerId, array $input);

    /**
     * Delete customer by identifier.
     *
     * @param int $customerId
     *
     * @return void
     */
    public function delete($customerId);

    /**
     * Search customers.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
