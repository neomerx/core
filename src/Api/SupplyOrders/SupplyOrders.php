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
use \Neomerx\Core\Api\Traits\InputParserTrait;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SupplyOrders implements SupplyOrdersInterface
{
    use InputParserTrait;
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
        list($orderData, $detailsData) = $this->parseInputOnCreate($input);

        /** @var array $orderData */
        /** @var array $detailsData */

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
            $supplyOrder = $this->orderModel->createOrFailResource($orderData);
            Permissions::check($supplyOrder, Permission::create());

            $supplyOrderId = $supplyOrder->{SupplyOrder::FIELD_ID};
            foreach ($detailsData as $detailsRow) {

                /** @noinspection PhpUndefinedMethodInspection */
                /** @var \Neomerx\Core\Models\SupplyOrderDetails $details */
                $details = App::make(SupplyOrderDetails::BIND_NAME);
                $details->fill($detailsRow);

                $details->{SupplyOrderDetails::FIELD_ID_SUPPLY_ORDER} = $supplyOrderId;
                $details->saveOrFail();
                Permissions::check($details, Permission::create());

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

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\SupplyOrderDetails $details */
        $details = App::make(SupplyOrderDetails::BIND_NAME);
        $details->fill($detailsData);
        $details->{SupplyOrderDetails::FIELD_ID_SUPPLY_ORDER} = $supplyOrder->{SupplyOrder::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $details->saveOrFail();
            Permissions::check($details, Permission::create());

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
     * @return array<array>
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function parseInputOnCreate(array $input)
    {
        $this->parseAndReplaceSupplyOrderInput($input);

        $statusName    = S\array_get_value($input, self::PARAM_STATUS, SupplyOrder::STATUS_DRAFT);
        $isStatusValid = in_array($statusName, [SupplyOrder::STATUS_DRAFT, SupplyOrder::STATUS_VALIDATED]);
        $isStatusValid ? : S\throwEx(new InvalidArgumentException(self::PARAM_STATUS));
        unset($input[self::PARAM_STATUS]);
        $input[SupplyOrder::FIELD_STATUS] = $statusName;

        $details = S\array_get_value($input, self::PARAM_DETAILS);
        unset($input[self::PARAM_DETAILS]);

        // if order details is not an array or the detail are empty for Validated order then throw exception
        // it's ok to create Draft order with no details but Validated must have some details
        if (!is_array($details) or ($statusName === SupplyOrder::STATUS_VALIDATED and empty($details))) {
            throw new InvalidArgumentException(self::PARAM_DETAILS);
        }

        $parsedDetails = [];
        foreach ($details as $detailsRow) {
            $parsedDetails[] = $this->parseDetailInputOnCreate($detailsRow);
        }

        return [$input, $parsedDetails];
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
        $this->parseAndReplaceSupplyOrderInput($input);

        /** @var \Neomerx\Core\Models\SupplyOrder $supplyOrder */
        $supplyOrder = $this->orderModel->findOrFail($supplyOrderId);

        $statusName = S\array_get_value($input, self::PARAM_STATUS);
        unset($input[self::PARAM_STATUS]);
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
            $input[SupplyOrder::FIELD_STATUS] = $statusName;
        }

        return [$supplyOrder, $input];
    }

    /**
     * @param array $detailsRow
     *
     * @return array
     */
    private function parseDetailInputOnCreate($detailsRow)
    {
        $this->replaceInputCodeWithId(
            $detailsRow,
            self::PARAM_DETAILS_SKU,
            $this->variantModel,
            Variant::FIELD_ID,
            SupplyOrderDetails::FIELD_ID_VARIANT
        );
        return $detailsRow;
    }

    /**
     * @param array &$input
     */
    private function parseAndReplaceSupplyOrderInput(array &$input)
    {
        $this->replaceInputCodeWithId(
            $input,
            self::PARAM_SUPPLIER_CODE,
            $this->supplierModel,
            Supplier::FIELD_ID,
            SupplyOrder::FIELD_ID_SUPPLIER
        );

        $this->replaceInputCodeWithId(
            $input,
            self::PARAM_WAREHOUSE_CODE,
            $this->warehouseModel,
            Warehouse::FIELD_ID,
            SupplyOrder::FIELD_ID_WAREHOUSE
        );

        $this->replaceInputCodeWithId(
            $input,
            self::PARAM_CURRENCY_CODE,
            $this->currencyModel,
            Currency::FIELD_ID,
            SupplyOrder::FIELD_ID_CURRENCY
        );

        $this->replaceInputCodeWithId(
            $input,
            self::PARAM_LANGUAGE_CODE,
            $this->languageModel,
            Language::FIELD_ID,
            SupplyOrder::FIELD_ID_LANGUAGE
        );

        $expectedAtInput = S\array_get_value($input, self::PARAM_EXPECTED_AT);
        if (!empty($expectedAtInput)) {
            $expectedAt = Carbon::parse($expectedAtInput);
            Carbon::now()->lt($expectedAt) ? : S\throwEx(new InvalidArgumentException(self::PARAM_EXPECTED_AT));
        }
    }
}
