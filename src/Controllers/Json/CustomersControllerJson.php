<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Customers;
use \Neomerx\Core\Converters\AddressConverterCustomer;
use \Neomerx\Core\Converters\CustomerConverterWithAddress;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class CustomersControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Customers::INTERFACE_BIND_NAME, App::make(CustomerConverterWithAddress::BIND_NAME));
    }

    /**
     * Search customers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input]);
    }

    /**
     * Get customer's addresses.
     *
     * @param int $customerId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function indexAddresses($customerId)
    {
        settype($customerId, 'int');
        return $this->tryAndCatchWrapper('indexAddressesImpl', [$customerId]);
    }

    /**
     * @param int $customerId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeAddress($customerId)
    {
        settype($customerId, 'int');
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('storeAddressImpl', [$customerId, $input]);
    }

    /**
     * Delete customer's address.
     *
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyAddress($customerId, $addressId, $type)
    {
        settype($customerId, 'int');
        settype($addressId, 'int');
        settype($type, 'string');
        return $this->tryAndCatchWrapper('destroyAddressImpl', [$customerId, $addressId, $type]);
    }

    /**
     * Set customer's default address.
     *
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function setDefaultAddress($customerId, $addressId, $type)
    {
        settype($customerId, 'int');
        settype($addressId, 'int');
        settype($type, 'string');
        return $this->tryAndCatchWrapper('setDefaultAddressImpl', [$customerId, $addressId, $type]);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function createResource(array $input)
    {
        $customer = $this->getApiFacade()->create($input);
        return [['id' => $customer->{Customer::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function searchImpl(array $parameters)
    {
        $customers = $this->getApiFacade()->search($parameters);

        $result = [];
        foreach ($customers as $customer) {
            /** @var Customer $customer */
            $result[] = $this->getConverter()->convert($customer);
        }

        return [$result, null];
    }

    /**
     * @param int   $customerId
     *
     * @return array
     */
    protected function indexAddressesImpl($customerId)
    {
        $addresses = $this->getApiFacade()->getAddresses($this->getModelById(Customer::BIND_NAME, $customerId));

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var AddressConverterCustomer $addressConverter */
        $addressConverter = App::make(AddressConverterCustomer::BIND_NAME);

        $result = [];
        foreach ($addresses as $address) {
            $result[] = $addressConverter->convert($address);
        }

        return [$result, null];
    }

    /**
     * @param int   $customerId
     * @param array $input
     *
     * @return array
     */
    protected function storeAddressImpl($customerId, array $input)
    {
        $address = $this->getApiFacade()->createAddress($this->getModelById(Customer::BIND_NAME, $customerId), $input);
        return [['id' => $address->{Address::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     *
     * @return array
     */
    protected function destroyAddressImpl($customerId, $addressId, $type)
    {
        $this->getApiFacade()->deleteAddress(
            $this->getModelById(Customer::BIND_NAME, $customerId),
            $this->getModelById(Address::BIND_NAME, $addressId),
            $type
        );
        return [null, null];
    }

    /**
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     *
     * @return array
     */
    protected function setDefaultAddressImpl($customerId, $addressId, $type)
    {
        $addresses = $this->getApiFacade()->setDefaultAddress(
            $this->getModelById(Customer::BIND_NAME, $customerId),
            $this->getModelById(Address::BIND_NAME, $addressId),
            $type
        );
        return [$addresses, null];
    }
}
