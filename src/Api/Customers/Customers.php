<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Api\Addresses\Addresses;
use \Illuminate\Database\Eloquent\Builder;
use \Neomerx\Core\Auth\Facades\Permissions;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customers extends CustomerAddresses implements CustomersInterface
{
    const EVENT_PREFIX = 'Api.Customer.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Customer
     */
    private $customerModel;

    /**
     * @var CustomerRisk
     */
    private $riskModel;

    /**
     * @var CustomerType
     */
    private $typeModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        Customer::FIELD_FIRST_NAME => SearchGrammar::TYPE_STRING,
        Customer::FIELD_LAST_NAME  => SearchGrammar::TYPE_STRING,
        Customer::FIELD_EMAIL      => SearchGrammar::TYPE_STRING,
        Customer::FIELD_MOBILE     => SearchGrammar::TYPE_STRING,
        Customer::FIELD_GENDER     => SearchGrammar::TYPE_STRING,
        'created'                  => [SearchGrammar::TYPE_DATE, Customer::FIELD_CREATED_AT],
        'updated'                  => [SearchGrammar::TYPE_DATE, Customer::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP  => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE  => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Customer        $customer
     * @param CustomerRisk    $customerRisk
     * @param Addresses       $addressApi
     * @param CustomerAddress $customerAddress
     * @param Region          $region
     * @param Language        $language
     * @param CustomerType    $customerType
     */
    public function __construct(
        Customer $customer,
        CustomerRisk $customerRisk,
        Addresses $addressApi,
        CustomerAddress $customerAddress,
        Region $region,
        Language $language,
        CustomerType $customerType
    ) {
        parent::__construct($customer, $addressApi, $customerAddress, $region);

        $this->customerModel         = $customer;
        $this->riskModel     = $customerRisk;
        $this->languageModel = $language;
        $this->typeModel     = $customerType;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        $input = $this->replaceCodesWithIds($input);

        // customer risk will be assigned by the system later
        unset($input[CustomerRisk::FIELD_ID]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Customer $customer */
            $customer = $this->customerModel->createOrFailResource($input);
            Permissions::check($customer, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new CustomerArgs(self::EVENT_PREFIX . 'created', $customer));

        return $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function read($customerId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\Customer $customer */
        $customer = $this->customerModel->newQuery()
            ->withTypeRiskAndLanguage()
            ->withDefaultAddresses()
            ->findOrFail($customerId);

        Permissions::check($customer, Permission::view());

        return $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, array $input)
    {
        $input = $this->replaceCodesWithIds($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Customer $customer */
            $customer = $this->customerModel->findOrFail($customerId);
            Permissions::check($customer, Permission::edit());
            empty($input) ?: $customer->updateOrFail($input);

            // customer could be changed in invoked method(s)
            $customer->isDirty() ? $customer->save() : null;

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new CustomerArgs(self::EVENT_PREFIX . 'updated', $customer));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($customerId)
    {
        /** @var Customer $customer */
        $customer = $this->customerModel->findOrFail($customerId);
        Permissions::check($customer, Permission::delete());
        $customer->deleteOrFail();

        Event::fire(new CustomerArgs(self::EVENT_PREFIX . 'deleted', $customer));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Builder $builder */
        $builder = $this->customerModel->newQuery()->withTypeRiskAndLanguage()->withDefaultAddresses();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $customers = $builder->get();

        foreach ($customers as $customer) {
            /** @var \Neomerx\Core\Models\Customer $customer */
            Permissions::check($customer, Permission::view());
        }

        return $customers;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function replaceCodesWithIds(array $input)
    {
        unset($input[Language::FIELD_ID]);
        if (isset($input[self::PARAM_LANGUAGE_CODE])) {
            $typeCode = $input[self::PARAM_LANGUAGE_CODE];
            $typeId   = $this->languageModel->selectByCode($typeCode)
                ->firstOrFail([Language::FIELD_ID])->{Language::FIELD_ID};
            $input = array_merge($input, [Language::FIELD_ID => $typeId]);
            unset($input[self::PARAM_LANGUAGE_CODE]);
        }

        unset($input[CustomerRisk::FIELD_ID]);
        if (isset($input[self::PARAM_RISK_CODE])) {
            $riskCode = $input[self::PARAM_RISK_CODE];
            $riskId   = $this->riskModel->selectByCode($riskCode)
                ->firstOrFail([CustomerRisk::FIELD_ID])->{CustomerRisk::FIELD_ID};
            $input = array_merge($input, [CustomerRisk::FIELD_ID => $riskId]);
            unset($input[self::PARAM_RISK_CODE]);
        }

        unset($input[CustomerType::FIELD_ID]);
        if (isset($input[self::PARAM_TYPE_CODE])) {
            $typeCode = $input[self::PARAM_TYPE_CODE];
            $typeId   = $this->typeModel->selectByCode($typeCode)
                ->firstOrFail([CustomerType::FIELD_ID])->{CustomerType::FIELD_ID};
            $input = array_merge($input, [CustomerType::FIELD_ID => $typeId]);
            unset($input[self::PARAM_TYPE_CODE]);
        }

        return $input;
    }
}
