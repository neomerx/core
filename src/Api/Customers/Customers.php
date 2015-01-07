<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Customer as Model;
use \Illuminate\Database\Eloquent\Builder;
use \Neomerx\Core\Models\Region as RegionModel;
use \Neomerx\Core\Models\CustomerRisk as RiskModel;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Models\CustomerType as TypeModel;
use \Neomerx\Core\Api\Addresses\Addresses as AddressesApi;
use \Neomerx\Core\Models\CustomerAddress as CustomerAddressModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customers extends CustomerAddresses implements CustomersInterface
{
    const EVENT_PREFIX = 'Api.Customer.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var RiskModel
     */
    private $riskModel;

    /**
     * @var TypeModel
     */
    private $typeModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        Model::FIELD_FIRST_NAME   => SearchGrammar::TYPE_STRING,
        Model::FIELD_LAST_NAME    => SearchGrammar::TYPE_STRING,
        Model::FIELD_EMAIL        => SearchGrammar::TYPE_STRING,
        Model::FIELD_MOBILE       => SearchGrammar::TYPE_STRING,
        Model::FIELD_GENDER       => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Model                $model
     * @param RiskModel            $customerRisk
     * @param AddressesApi         $addressApi
     * @param CustomerAddressModel $customerAddress
     * @param RegionModel          $region
     * @param LanguageModel        $language
     * @param TypeModel            $customerType
     */
    public function __construct(
        Model $model,
        RiskModel $customerRisk,
        AddressesApi $addressApi,
        CustomerAddressModel $customerAddress,
        RegionModel $region,
        LanguageModel $language,
        TypeModel $customerType
    ) {
        parent::__construct($model, $addressApi, $customerAddress, $region);

        $this->model         = $model;
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
        unset($input[RiskModel::FIELD_ID]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $customer */
            $customer = $this->model->createOrFailResource($input);
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
        /** @var Model $customer */
        $customer = $this->model->newQuery()
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

            /** @var Model $customer */
            $customer = $this->model->findOrFail($customerId);
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
        /** @var Model $customer */
        $customer = $this->model->findOrFail($customerId);
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
        $builder = $this->model->newQuery()->withTypeRiskAndLanguage()->withDefaultAddresses();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $customers = $builder->get();

        foreach ($customers as $customer) {
            /** @var Model $customer */
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
        unset($input[LanguageModel::FIELD_ID]);
        if (isset($input[self::PARAM_LANGUAGE_CODE])) {
            $typeCode = $input[self::PARAM_LANGUAGE_CODE];
            $typeId   = $this->languageModel->selectByCode($typeCode)
                ->firstOrFail([LanguageModel::FIELD_ID])->{LanguageModel::FIELD_ID};
            $input = array_merge($input, [LanguageModel::FIELD_ID => $typeId]);
            unset($input[self::PARAM_LANGUAGE_CODE]);
        }

        unset($input[RiskModel::FIELD_ID]);
        if (isset($input[self::PARAM_RISK_CODE])) {
            $riskCode = $input[self::PARAM_RISK_CODE];
            $riskId   = $this->riskModel->selectByCode($riskCode)
                ->firstOrFail([RiskModel::FIELD_ID])->{RiskModel::FIELD_ID};
            $input = array_merge($input, [RiskModel::FIELD_ID => $riskId]);
            unset($input[self::PARAM_RISK_CODE]);
        }

        unset($input[TypeModel::FIELD_ID]);
        if (isset($input[self::PARAM_TYPE_CODE])) {
            $typeCode = $input[self::PARAM_TYPE_CODE];
            $typeId   = $this->typeModel->selectByCode($typeCode)
                ->firstOrFail([TypeModel::FIELD_ID])->{TypeModel::FIELD_ID};
            $input = array_merge($input, [TypeModel::FIELD_ID => $typeId]);
            unset($input[self::PARAM_TYPE_CODE]);
        }

        return $input;
    }
}
