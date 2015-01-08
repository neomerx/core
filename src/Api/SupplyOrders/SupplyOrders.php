<?php namespace Neomerx\Core\Api\SupplyOrders;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SupplyOrders implements SupplyOrdersInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.SupplyOrder.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var array
     */
    protected static $supplyOrderRelations = [
        SupplyOrder::FIELD_LANGUAGE,
        SupplyOrder::FIELD_WAREHOUSE,
        'supplier.properties.language',
        'currency.properties.language',
        'details.variant.properties.language',
    ];

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        SupplyOrder::FIELD_STATUS => SearchGrammar::TYPE_STRING,
        'expected'                => [SearchGrammar::TYPE_DATE, SupplyOrder::FIELD_EXPECTED_AT],
        'created'                 => [SearchGrammar::TYPE_DATE, SupplyOrder::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, SupplyOrder::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @var SupplyOrder
     */
    private $orderModel;

    /**
     * @var SupplyOrderDetails
     */
    private $detailsModel;

    /**
     * @var Supplier
     */
    private $supplierModel;

    /**
     * @var Warehouse
     */
    private $warehouseModel;

    /**
     * @var Currency
     */
    private $currencyModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @param SupplyOrder        $order
     * @param SupplyOrderDetails $details
     * @param Supplier           $supplier
     * @param Warehouse          $warehouse
     * @param Currency           $currency
     * @param Language           $language
     * @param Product            $product
     * @param Variant            $variant
     */
    public function __construct(
        SupplyOrder $order,
        SupplyOrderDetails  $details,
        Supplier $supplier,
        Warehouse $warehouse,
        Currency $currency,
        Language $language,
        Product $product,
        Variant $variant
    ) {
        $this->orderModel     = $order;
        $this->detailsModel   = $details;
        $this->supplierModel  = $supplier;
        $this->warehouseModel = $warehouse;
        $this->currencyModel  = $currency;
        $this->languageModel  = $language;
        $this->productModel   = $product;
        $this->variantModel   = $variant;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($orderData, $details) = $this->parseInputOnCreate($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
            $supplyOrder = $this->orderModel->createOrFailResource($orderData);
            Permissions::check($supplyOrder, Permission::create());

            foreach ($details as $detailsRow) {
                $details = new SupplyOrderDetails($detailsRow);
                /** @noinspection PhpUndefinedMethodInspection */
                /** @var \Neomerx\Core\Models\SupplyOrderDetails $saved */
                $saved = $supplyOrder->details()->save($details);
                if (isset($saved) and $saved->exists) {
                    Permissions::check($saved, Permission::create());
                } else {
                    throw new ValidationException($saved->getValidator());
                }
            }

            Event::fire(new SupplyOrderArgs(self::EVENT_PREFIX . 'creating', $supplyOrder));

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new SupplyOrderArgs(self::EVENT_PREFIX . 'created', $supplyOrder));

        return $supplyOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function createDetails(SupplyOrder $supplyOrder, array $input)
    {
        $detailsData = $this->parseDetailInputOnCreate($input);
        /** @var \Neomerx\Core\Models\SupplyOrderDetails $details */
        /** @noinspection PhpUndefinedMethodInspection */
        $details = App::make(SupplyOrderDetails::BIND_NAME);
        $details->fill($detailsData);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @noinspection PhpUndefinedMethodInspection */
            $details = $supplyOrder->details()->save($details);
            /** @var \Neomerx\Core\Models\SupplyOrderDetails $details */
            if (isset($details) and $details->exists) {
                Permissions::check($details, Permission::create());
            } else {
                throw new ValidationException($details->getValidator());
            }

            Event::fire(new SupplyOrderDetailsArgs(self::EVENT_PREFIX . 'detailsCreating', $details));

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new SupplyOrderDetailsArgs(self::EVENT_PREFIX . 'detailsCreated', $details));

        return $details;
    }

    /**
     * {@inheritdoc}
     */
    public function read($supplyOrderId)
    {
        /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
        $supplyOrder = $this->orderModel->with(static::$supplyOrderRelations)->findOrFail($supplyOrderId);
        Permissions::check($supplyOrder, Permission::view());
        return $supplyOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function readDetails($detailsId)
    {
        /** @var \Neomerx\Core\Models\SupplyOrderDetails $detailsRow */
        $detailsRow = $this->detailsModel->findOrFail($detailsId);
        Permissions::check($detailsRow, Permission::view());
        return $detailsRow;
    }

    /**
     * {@inheritdoc}
     */
    public function update($supplyOrderId, array $input)
    {
        /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
        list($supplyOrder, $orderInput) = $this->parseInputOnUpdate($supplyOrderId, $input);
        Permissions::check($supplyOrder, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $supplyOrder->fill($orderInput);
            Event::fire(new SupplyOrderArgs(self::EVENT_PREFIX . 'updating', $supplyOrder));
            $supplyOrder->save();

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new SupplyOrderArgs(self::EVENT_PREFIX . 'updated', $supplyOrder));
    }

    /**
     * {@inheritdoc}
     */
    public function updateDetails(SupplyOrderDetails $detailsRow, array $input)
    {
        Permissions::check($detailsRow, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $detailsRow->fill($input);
            Event::fire(new SupplyOrderDetailsArgs(self::EVENT_PREFIX . 'detailsUpdating', $detailsRow));
            $detailsRow->save();

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new SupplyOrderDetailsArgs(self::EVENT_PREFIX . 'detailsUpdated', $detailsRow));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($supplyOrderId)
    {
        /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
        $supplyOrder = $this->orderModel->findOrFail($supplyOrderId);

        Permissions::check($supplyOrder, Permission::delete());

        $supplyOrder->deleteOrFail();

        Event::fire(new SupplyOrderArgs(self::EVENT_PREFIX . 'deleted', $supplyOrder));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDetails($detailsId)
    {
        /** @var \Neomerx\Core\Models\SupplyOrderDetails $details */
        $details = $this->detailsModel->findOrFail($detailsId);

        Permissions::check($details, Permission::delete());

        $details->deleteOrFail();

        Event::fire(new SupplyOrderDetailsArgs(self::EVENT_PREFIX . 'detailsDeleted', $details));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpParamsInspection */
        $builder = $this->orderModel->newQuery()->with(static::$supplyOrderRelations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $resources = $builder->get();

        foreach ($resources as $order) {
            /** @var \Neomerx\Core\Models\SupplyOrder $order */
            Permissions::check($order, Permission::view());
        }

        return $resources;
    }

    /**
     * @param array $input
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function parseInputOnCreate(array $input)
    {
        $supplierCode  =  S\array_get_value($input, self::PARAM_SUPPLIER_CODE);
        $supplierCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_SUPPLIER_CODE));
        unset($input[self::PARAM_SUPPLIER_CODE]);

        $warehouseCode  =  S\array_get_value($input, self::PARAM_WAREHOUSE_CODE);
        $warehouseCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_WAREHOUSE_CODE));
        unset($input[self::PARAM_WAREHOUSE_CODE]);

        $currencyCode  =  S\array_get_value($input, self::PARAM_CURRENCY_CODE);
        $currencyCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_CURRENCY_CODE));
        unset($input[self::PARAM_CURRENCY_CODE]);

        $languageCode  =  S\array_get_value($input, self::PARAM_LANGUAGE_CODE);
        $languageCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_LANGUAGE_CODE));
        unset($input[self::PARAM_LANGUAGE_CODE]);

        $expectedAt   = S\array_get_value($input, self::PARAM_EXPECTED_AT);
        $expectedAt  !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_EXPECTED_AT));
        $expectedAt   = Carbon::parse($expectedAt);
        $expectedAt  !== false ?: S\throwEx(new InvalidArgumentException(self::PARAM_EXPECTED_AT));
        Carbon::now()->lt($expectedAt) ?: S\throwEx(new InvalidArgumentException(self::PARAM_EXPECTED_AT));

        $statusName    = S\array_get_value($input, self::PARAM_STATUS, SupplyOrder::STATUS_DRAFT);
        $isStatusValid = in_array($statusName, [SupplyOrder::STATUS_DRAFT, SupplyOrder::STATUS_VALIDATED]);
        $isStatusValid ? : S\throwEx(new InvalidArgumentException(self::PARAM_STATUS));

        $details = S\array_get_value($input, self::PARAM_DETAILS);
        ($details !== null and is_array($details)) ?: S\throwEx(new InvalidArgumentException(self::PARAM_DETAILS));
        unset($input[self::PARAM_DETAILS]);

        // order should have details, if no details in validated order throw exception
        $noDetailsInValidated = ($statusName === SupplyOrder::STATUS_VALIDATED and empty($details));
        $noDetailsInValidated ? S\throwEx(new InvalidArgumentException(self::PARAM_DETAILS)) : null;

        /** @var \Neomerx\Core\Models\Supplier $supplier */
        $supplier = $this->supplierModel->selectByCode($supplierCode)->firstOrFail([Supplier::FIELD_ID]);
        Permissions::check($supplier, Permission::view());

        /** @var \Neomerx\Core\Models\Warehouse $warehouse */
        $warehouse = $this->warehouseModel->selectByCode($warehouseCode)->firstOrFail([Warehouse::FIELD_ID]);
        Permissions::check($warehouse, Permission::view());

        /** @var \Neomerx\Core\Models\Currency $currency */
        $currency = $this->currencyModel->selectByCode($currencyCode)->firstOrFail([Currency::FIELD_ID]);
        Permissions::check($currency, Permission::view());

        /** @var \Neomerx\Core\Models\Language $language */
        $language = $this->languageModel->selectByCode($languageCode)->firstOrFail([Language::FIELD_ID]);
        Permissions::check($language, Permission::view());

        $parsedDetails = [];
        foreach ($details as $detailsRow) {
            $parsedDetails[] = $this->parseDetailInputOnCreate($detailsRow);
        }

        $orderData = array_merge($input, [
            SupplyOrder::FIELD_ID_WAREHOUSE => $warehouse->{Warehouse::FIELD_ID},
            SupplyOrder::FIELD_ID_SUPPLIER  => $supplier->{Supplier::FIELD_ID},
            SupplyOrder::FIELD_ID_CURRENCY  => $currency->{Currency::FIELD_ID},
            SupplyOrder::FIELD_ID_LANGUAGE  => $language->{Language::FIELD_ID},
            SupplyOrder::FIELD_STATUS       => $statusName,
        ]);

        return [$orderData, $parsedDetails];
    }

    /**
     * @param int   $supplyOrderId
     * @param array $input
     *
     * @return array
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function parseInputOnUpdate($supplyOrderId, array $input)
    {
        $supplierCode = S\array_get_value($input, self::PARAM_SUPPLIER_CODE);
        unset($input[self::PARAM_SUPPLIER_CODE]);

        $warehouseCode = S\array_get_value($input, self::PARAM_WAREHOUSE_CODE);
        unset($input[self::PARAM_WAREHOUSE_CODE]);

        $currencyCode = S\array_get_value($input, self::PARAM_CURRENCY_CODE);
        unset($input[self::PARAM_CURRENCY_CODE]);

        $languageCode  =  S\array_get_value($input, self::PARAM_LANGUAGE_CODE);
        unset($input[self::PARAM_LANGUAGE_CODE]);

        $expectedAt  = S\array_get_value($input, self::PARAM_EXPECTED_AT);
        if ($expectedAt !== null) {
            $expectedAt = Carbon::parse($expectedAt);
            if (Carbon::now()->gt($expectedAt)) {
                throw new InvalidArgumentException(self::PARAM_EXPECTED_AT);
            }
        }

        $parsedOrderInput = [];
        /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
        $supplyOrder = $this->orderModel->findOrFail($supplyOrderId);

        $statusName = S\array_get_value($input, self::PARAM_STATUS);
        if ($statusName !== null) {
            $currentStatus = $supplyOrder->status;
            if ($currentStatus === SupplyOrder::STATUS_DRAFT) {
                $isStatusValid = in_array($statusName, [SupplyOrder::STATUS_VALIDATED, SupplyOrder::STATUS_CANCELLED]);
            } elseif ($currentStatus === SupplyOrder::STATUS_VALIDATED) {
                $isStatusValid = in_array($statusName, [SupplyOrder::STATUS_CANCELLED]);
            } else {
                $isStatusValid = false;
            }
            $isStatusValid ? : S\throwEx(new InvalidArgumentException(self::PARAM_STATUS));
            $parsedOrderInput['status'] = $statusName;
        }

        if ($supplierCode !== null) {
            /** @var \Neomerx\Core\Models\Supplier $supplier */
            $supplier = $this->supplierModel->selectByCode($supplierCode)->firstOrFail([Supplier::FIELD_ID]);
            Permissions::check($supplier, Permission::view());
            $parsedOrderInput[Supplier::FIELD_ID] = $supplier->{Supplier::FIELD_ID};
        }

        if ($warehouseCode !== null) {
            /** @var \Neomerx\Core\Models\Warehouse $warehouse */
            $warehouse = $this->warehouseModel->selectByCode($warehouseCode)->firstOrFail([Warehouse::FIELD_ID]);
            Permissions::check($warehouse, Permission::view());
            $parsedOrderInput[Warehouse::FIELD_ID] = $warehouse->{Warehouse::FIELD_ID};
        }

        if ($currencyCode !== null) {
            /** @var \Neomerx\Core\Models\Currency $currency */
            $currency = $this->currencyModel->selectByCode($currencyCode)->firstOrFail([Currency::FIELD_ID]);
            Permissions::check($currency, Permission::view());
            $parsedOrderInput[Currency::FIELD_ID] = $currency->{Currency::FIELD_ID};
        }

        if ($languageCode !== null) {
            /** @var \Neomerx\Core\Models\Language $language */
            $language = $this->languageModel->selectByCode($languageCode)->firstOrFail([Language::FIELD_ID]);
            Permissions::check($language, Permission::view());
            $parsedOrderInput[Language::FIELD_ID] = $language->{Language::FIELD_ID};
        }

        return [$supplyOrder, array_merge($input, $parsedOrderInput)];
    }

    /**
     * @param array $detailsRow
     *
     * @return array
     */
    private function parseDetailInputOnCreate($detailsRow)
    {
        $sku  =  S\array_get_value($detailsRow, self::PARAM_DETAILS_SKU);
        $sku !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_DETAILS));
        unset($detailsRow[self::PARAM_DETAILS_SKU]);

        $itId = $this->variantModel->selectByCode($sku)->firstOrFail([Variant::FIELD_ID])->{Variant::FIELD_ID};

        return array_merge($detailsRow, [
            SupplyOrderDetails::FIELD_ID_VARIANT => $itId,
        ]);
    }
}
