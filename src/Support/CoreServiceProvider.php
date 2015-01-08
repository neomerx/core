<?php namespace Neomerx\Core\Support;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Api\Users\Roles;
use \Neomerx\Core\Api\Taxes\Taxes;
use \Neomerx\Core\Api\Users\Users;
use \Neomerx\Core\Api\Orders\Orders;
use \Neomerx\Core\Api\Stores\Stores;
use \Neomerx\Core\Api\Taxes\TaxRules;
use \Neomerx\Core\Api\Carriers\Carriers;
use \Illuminate\Support\ServiceProvider;
use \Neomerx\Core\Api\Features\Features;
use \Neomerx\Core\Api\Products\Products;
use \Neomerx\Core\Api\Login\LoginService;
use \Neomerx\Core\Api\Addresses\Addresses;
use \Neomerx\Core\Api\Customers\Customers;
use \Neomerx\Core\Api\Images\ImageFormats;
use \Neomerx\Core\Api\Inventory\Inventories;
use \Neomerx\Core\Api\Languages\Languages;
use \Neomerx\Core\Api\Territories\Regions;
use \Neomerx\Core\Api\Suppliers\Suppliers;
use \Illuminate\Support\Facades\Validator;
use \Neomerx\Core\Api\Orders\OrderStatuses;
use \Neomerx\Core\Auth\PermissionManagement;
use \Neomerx\Core\Api\Territories\Countries;
use \Neomerx\Core\Api\Categories\Categories;
use \Neomerx\Core\Api\Features\Measurements;
use \Neomerx\Core\Api\Currencies\Currencies;
use \Neomerx\Core\Api\Warehouses\Warehouses;
use \Neomerx\Core\Api\Customers\CustomerTypes;
use \Neomerx\Core\Api\Products\ProductTaxTypes;
use \Neomerx\Core\Api\SupplyOrders\SupplyOrders;
use \Neomerx\Core\Api\Customers\CustomerAddresses;
use \Neomerx\Core\Api\Manufacturers\Manufacturers;
use \Neomerx\Core\Api\Facades\Login as LoginFacade;
use \Neomerx\Core\Api\Facades\Roles as RolesFacade;
use \Neomerx\Core\Api\Facades\Taxes as TaxesFacade;
use \Neomerx\Core\Api\Facades\Users as UsersFacade;
use \Neomerx\Core\Api\ShippingOrders\ShippingOrders;
use \Neomerx\Core\Auth\PermissionManagementInterface;
use \Neomerx\Core\Converters\AddressConverterGeneric;
use \Neomerx\Core\Api\Facades\Orders as OrdersFacade;
use \Neomerx\Core\Api\Facades\Stores as StoresFacade;
use \Neomerx\Core\Api\ShippingOrders\ShippingStatuses;
use \Neomerx\Core\Converters\AddressConverterCustomer;
use \Neomerx\Core\Converters\CustomerConverterGeneric;
use \Neomerx\Core\Api\Facades\Regions as RegionsFacade;
use \Neomerx\Core\Api\Facades\Carriers as CarriersFacade;
use \Neomerx\Core\Api\Facades\Features as FeaturesFacade;
use \Neomerx\Core\Api\Facades\Products as ProductsFacade;
use \Neomerx\Core\Api\Facades\TaxRules as TaxRulesFacade;
use \Neomerx\Core\Converters\CustomerConverterWithAddress;
use \Neomerx\Core\Api\Facades\Addresses as AddressesFacade;
use \Neomerx\Core\Api\Facades\Countries as CountriesFacade;
use \Neomerx\Core\Api\Facades\Customers as CustomersFacade;
use \Neomerx\Core\Api\Facades\Inventories as InventoryFacade;
use \Neomerx\Core\Api\Facades\Languages as LanguagesFacade;
use \Neomerx\Core\Api\Facades\Suppliers as SuppliersFacade;
use \Neomerx\Core\Api\Facades\Categories as CategoriesFacade;
use \Neomerx\Core\Api\Facades\Currencies as CurrenciesFacade;
use \Neomerx\Core\Api\Facades\Warehouses as WarehousesFacade;
use \Neomerx\Core\Api\Facades\ImageFormats as ImageFormatsFacade;
use \Neomerx\Core\Api\Facades\SupplyOrders as SupplyOrdersFacade;
use \Neomerx\Core\Api\Facades\Measurements as MeasurementsFacade;
use \Neomerx\Core\Api\Facades\CustomerTypes as CustomerTypesFacade;
use \Neomerx\Core\Api\Facades\Manufacturers as ManufacturersFacade;
use \Neomerx\Core\Api\Facades\OrderStatuses as OrderStatusesFacade;
use \Neomerx\Core\Api\Facades\ShippingOrders as ShippingOrdersFacade;
use \Neomerx\Core\Api\Facades\ProductTaxTypes as ProductTaxTypesFacade;
use \Neomerx\Core\Api\Facades\ShippingStatuses as ShippingStatusesFacade;
use \Neomerx\Core\Api\Facades\CustomerAddresses as CustomerAddressesFacade;

/**
 * Provides necessary Neomerx registrations in a single location. It bootstraps
 * such components as model facades, validation rules and commands.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CoreServiceProvider extends ServiceProvider
{
    const NEOMERX_PREFIX = 'nm';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // up to 'src' dir
        $resourceDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        $this->package('neomerx/core', self::NEOMERX_PREFIX, $resourceDir);
        /** @noinspection PhpIncludeInspection */
        include $resourceDir . 'filters.php';
        /** @noinspection PhpIncludeInspection */
        include $resourceDir . 'routes.php';

        $this->extendValidator();

        Event::setupHandlers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerApiFacades();

        $this->registerConverters();

        $this->registerSecurity();
    }

    /**
     * Register validation classes.
     */
    private function extendValidator()
    {
        // Validate value for having only characters, numbers, dots, dashes, underscores and spaces.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend(
            'alpha_dash_dot_space',
            function ($attribute, $value) {
                return preg_match('/^[\pL\pN\s\._-]+$/u', $value);
            },
            trans('nm::errors.validation_alpha_dash_dot_space')
        );

        // TODO add 'code' validation rules to all model codes

        // Validate value for having only characters, numbers, dots, underscores and dashes.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend(
            'code',
            function ($attribute, $value) {
                return preg_match('/^[\pL\pN\.\_\-]+$/u', $value);
            },
            trans('nm::errors.validation_code')
        );

        // Attribute marked with this rule must not be in the input data.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend(
            'forbidden',
            function () {
                return false;
            },
            trans('nm::errors.validation_forbidden')
        );
    }

    /**
     * Register Model Facades.
     */
    private function registerApiFacades()
    {
        $this->app->singleton(LoginFacade::INTERFACE_BIND_NAME, LoginService::BIND_NAME);

        $this->app->bind(RolesFacade::INTERFACE_BIND_NAME, Roles::BIND_NAME);
        $this->app->bind(TaxesFacade::INTERFACE_BIND_NAME, Taxes::BIND_NAME);
        $this->app->bind(UsersFacade::INTERFACE_BIND_NAME, Users::BIND_NAME);
        $this->app->bind(OrdersFacade::INTERFACE_BIND_NAME, Orders::BIND_NAME);
        $this->app->bind(StoresFacade::INTERFACE_BIND_NAME, Stores::BIND_NAME);
        $this->app->bind(RegionsFacade::INTERFACE_BIND_NAME, Regions::BIND_NAME);
        $this->app->bind(CarriersFacade::INTERFACE_BIND_NAME, Carriers::BIND_NAME);
        $this->app->bind(FeaturesFacade::INTERFACE_BIND_NAME, Features::BIND_NAME);
        $this->app->bind(ProductsFacade::INTERFACE_BIND_NAME, Products::BIND_NAME);
        $this->app->bind(TaxRulesFacade::INTERFACE_BIND_NAME, TaxRules::BIND_NAME);
        $this->app->bind(AddressesFacade::INTERFACE_BIND_NAME, Addresses::BIND_NAME);
        $this->app->bind(CountriesFacade::INTERFACE_BIND_NAME, Countries::BIND_NAME);
        $this->app->bind(CustomersFacade::INTERFACE_BIND_NAME, Customers::BIND_NAME);
        $this->app->bind(InventoryFacade::INTERFACE_BIND_NAME, Inventories::BIND_NAME);
        $this->app->bind(LanguagesFacade::INTERFACE_BIND_NAME, Languages::BIND_NAME);
        $this->app->bind(SuppliersFacade::INTERFACE_BIND_NAME, Suppliers::BIND_NAME);
        $this->app->bind(CategoriesFacade::INTERFACE_BIND_NAME, Categories::BIND_NAME);
        $this->app->bind(CurrenciesFacade::INTERFACE_BIND_NAME, Currencies::BIND_NAME);
        $this->app->bind(WarehousesFacade::INTERFACE_BIND_NAME, Warehouses::BIND_NAME);
        $this->app->bind(MeasurementsFacade::INTERFACE_BIND_NAME, Measurements::BIND_NAME);
        $this->app->bind(ImageFormatsFacade::INTERFACE_BIND_NAME, ImageFormats::BIND_NAME);
        $this->app->bind(SupplyOrdersFacade::INTERFACE_BIND_NAME, SupplyOrders::BIND_NAME);
        $this->app->bind(CustomerTypesFacade::INTERFACE_BIND_NAME, CustomerTypes::BIND_NAME);
        $this->app->bind(ManufacturersFacade::INTERFACE_BIND_NAME, Manufacturers::BIND_NAME);
        $this->app->bind(OrderStatusesFacade::INTERFACE_BIND_NAME, OrderStatuses::BIND_NAME);
        $this->app->bind(ShippingOrdersFacade::INTERFACE_BIND_NAME, ShippingOrders::BIND_NAME);
        $this->app->bind(ProductTaxTypesFacade::INTERFACE_BIND_NAME, ProductTaxTypes::BIND_NAME);
        $this->app->bind(ShippingStatusesFacade::INTERFACE_BIND_NAME, ShippingStatuses::BIND_NAME);
        $this->app->bind(CustomerAddressesFacade::INTERFACE_BIND_NAME, CustomerAddresses::BIND_NAME);
    }

    /**
     * Register security implementation.
     */
    private function registerSecurity()
    {
        $this->app
            ->singleton(PermissionManagementInterface::class, PermissionManagement::class);
    }

    /**
     * Register model converters.
     */
    private function registerConverters()
    {
        $this->app->bind(AddressConverterGeneric::BIND_NAME, AddressConverterGeneric::BIND_NAME, true);
        $this->app->bind(AddressConverterCustomer::BIND_NAME, AddressConverterCustomer::BIND_NAME, true);
        $this->app->bind(CustomerConverterGeneric::BIND_NAME, CustomerConverterGeneric::BIND_NAME, true);
        $this->app->bind(CustomerConverterWithAddress::BIND_NAME, CustomerConverterWithAddress::BIND_NAME, true);
    }
}
