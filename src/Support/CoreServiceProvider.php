<?php namespace Neomerx\Core\Support;

use \Neomerx\Core\Config;
use \Neomerx\Core\Filesystem\Images;
use \Illuminate\Support\Facades\Lang;
use \Illuminate\Support\ServiceProvider;
use \Neomerx\Core\Models\ProductTaxType;
use \Illuminate\Support\Facades\Validator;
use \Neomerx\Core\Filesystem\ImagesInterface;
use \Neomerx\Core\Repositories\Auth\RoleRepository;
use \Illuminate\Support\Facades\Config as SysConfig;
use \Neomerx\Core\Repositories\Images\ImageRepository;
use \Neomerx\Core\Repositories\Orders\OrderRepository;
use \Neomerx\Core\Repositories\Stores\StoreRepository;
use \Neomerx\Core\Repositories\Auth\EmployeeRepository;
use \Neomerx\Core\Repositories\Features\ValueRepository;
use \Neomerx\Core\Repositories\Images\ImagePathRepository;
use \Neomerx\Core\Repositories\Products\ProductRepository;
use \Neomerx\Core\Repositories\Products\VariantRepository;
use \Neomerx\Core\Repositories\Addresses\AddressRepository;
use \Neomerx\Core\Repositories\Auth\EmployeeRoleRepository;
use \Neomerx\Core\Repositories\Auth\RoleRepositoryInterface;
use \Neomerx\Core\Repositories\Customers\CustomerRepository;
use \Neomerx\Core\Repositories\Images\ImageFormatRepository;
use \Neomerx\Core\Repositories\Languages\LanguageRepository;
use \Neomerx\Core\Repositories\Orders\OrderStatusRepository;
use \Neomerx\Core\Repositories\Suppliers\SupplierRepository;
use \Neomerx\Core\Repositories\Territories\RegionRepository;
use \Neomerx\Core\Repositories\Categories\CategoryRepository;
use \Neomerx\Core\Repositories\Currencies\CurrencyRepository;
use \Neomerx\Core\Repositories\Orders\OrderHistoryRepository;
use \Neomerx\Core\Repositories\Orders\OrderInvoiceRepository;
use \Neomerx\Core\Repositories\Territories\CountryRepository;
use \Neomerx\Core\Repositories\Orders\OrderDetailsRepository;
use \Neomerx\Core\Repositories\Features\MeasurementRepository;
use \Neomerx\Core\Repositories\Orders\ShippingOrderRepository;
use \Neomerx\Core\Repositories\Warehouses\WarehouseRepository;
use \Neomerx\Core\Repositories\Images\ImageRepositoryInterface;
use \Neomerx\Core\Repositories\Inventories\InventoryRepository;
use \Neomerx\Core\Repositories\Orders\OrderRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\ShippingStatusRepository;
use \Neomerx\Core\Repositories\Products\ProductImageRepository;
use \Neomerx\Core\Repositories\Stores\StoreRepositoryInterface;
use \Neomerx\Core\Repositories\Suppliers\SupplyOrderRepository;
use \Neomerx\Core\Repositories\Auth\EmployeeRepositoryInterface;
use \Neomerx\Core\Repositories\Customers\CustomerRiskRepository;
use \Neomerx\Core\Repositories\Customers\CustomerTypeRepository;
use \Neomerx\Core\Repositories\Images\ImagePropertiesRepository;
use \Neomerx\Core\Repositories\Orders\OrderStatusRuleRepository;
use \Neomerx\Core\Repositories\Products\SpecificationRepository;
use \Neomerx\Core\Repositories\Features\CharacteristicRepository;
use \Neomerx\Core\Repositories\Features\ValueRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductRelatedRepository;
use \Neomerx\Core\Repositories\Products\ProductTaxTypeRepository;
use \Neomerx\Core\Repositories\Products\ProductCategoryRepository;
use \Neomerx\Core\Repositories\Features\ValuePropertiesRepository;
use \Neomerx\Core\Repositories\Customers\CustomerAddressRepository;
use \Neomerx\Core\Repositories\Images\ImagePathRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductRepositoryInterface;
use \Neomerx\Core\Repositories\Products\VariantRepositoryInterface;
use \Neomerx\Core\Repositories\Auth\EmployeeRoleRepositoryInterface;
use \Neomerx\Core\Repositories\Addresses\AddressRepositoryInterface;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerRepository;
use \Neomerx\Core\Repositories\Products\ProductPropertiesRepository;
use \Neomerx\Core\Repositories\Products\VariantPropertiesRepository;
use \Neomerx\Core\Repositories\Customers\CustomerRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImageFormatRepositoryInterface;
use \Neomerx\Core\Repositories\Languages\LanguageRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\OrderStatusRepositoryInterface;
use \Neomerx\Core\Repositories\Suppliers\SupplierRepositoryInterface;
use \Neomerx\Core\Repositories\Territories\RegionRepositoryInterface;
use \Neomerx\Core\Repositories\Categories\CategoryRepositoryInterface;
use \Neomerx\Core\Repositories\Currencies\CurrencyRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\OrderDetailsRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\OrderHistoryRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\OrderInvoiceRepositoryInterface;
use \Neomerx\Core\Repositories\Suppliers\SupplierPropertiesRepository;
use \Neomerx\Core\Repositories\Suppliers\SupplyOrderDetailsRepository;
use \Neomerx\Core\Repositories\Territories\CountryRepositoryInterface;
use \Neomerx\Core\Repositories\Categories\CategoryPropertiesRepository;
use \Neomerx\Core\Repositories\Currencies\CurrencyPropertiesRepository;
use \Neomerx\Core\Repositories\Features\MeasurementRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\ShippingOrderRepositoryInterface;
use \Neomerx\Core\Repositories\Territories\CountryPropertiesRepository;
use \Neomerx\Core\Repositories\Warehouses\WarehouseRepositoryInterface;
use \Neomerx\Core\Repositories\Features\MeasurementPropertiesRepository;
use \Neomerx\Core\Repositories\Inventories\InventoryRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\ShippingStatusRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductImageRepositoryInterface;
use \Neomerx\Core\Repositories\Suppliers\SupplyOrderRepositoryInterface;
use \Neomerx\Core\Repositories\Customers\CustomerRiskRepositoryInterface;
use \Neomerx\Core\Repositories\Customers\CustomerTypeRepositoryInterface;
use \Neomerx\Core\Repositories\Orders\OrderStatusRuleRepositoryInterface;
use \Neomerx\Core\Repositories\Products\SpecificationRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImagePropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Features\CharacteristicRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductRelatedRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductTaxTypeRepositoryInterface;
use \Neomerx\Core\Repositories\Features\CharacteristicPropertiesRepository;
use \Neomerx\Core\Repositories\Products\ProductCategoryRepositoryInterface;
use \Neomerx\Core\Repositories\Features\ValuePropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Customers\CustomerAddressRepositoryInterface;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerRepositoryInterface;
use \Neomerx\Core\Repositories\Products\ProductPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Products\VariantPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerPropertiesRepository;
use \Neomerx\Core\Repositories\Suppliers\SupplierPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Suppliers\SupplyOrderDetailsRepositoryInterface;
use \Neomerx\Core\Repositories\Categories\CategoryPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Currencies\CurrencyPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Territories\CountryPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Features\MeasurementPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Features\CharacteristicPropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerPropertiesRepositoryInterface;

/**
 * Provides necessary Neomerx registrations in a single location. It bootstraps
 * such components as facades, validation rules and etc.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CoreServiceProvider extends ServiceProvider
{
    const NEOMERX_PREFIX  = 'nm';
    const PACKAGE_NAME    = 'neomerx/core';
    const CONFIG_ROOT_KEY = self::PACKAGE_NAME;

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
        $resourceDir = realpath(
            __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR
        );

        /** @noinspection PhpUndefinedMethodInspection */
        Lang::addNamespace(self::NEOMERX_PREFIX, $resourceDir);
        /** @noinspection PhpUndefinedMethodInspection */
        SysConfig::set([self::CONFIG_ROOT_KEY => [
            /*
            |--------------------------------------------------------------------------
            | Image Folder
            |--------------------------------------------------------------------------
            |
            | The folder is used for uploading product images and storing them in
            | various image formats.
            |
            | This folder should be writable for the web server and end with
            | folder separator.
            |
            */
            Config::KEY_IMAGE_FOLDER => storage_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR,
            /*
            |--------------------------------------------------------------------------
            | Product tax type for shipping
            |--------------------------------------------------------------------------
            |
            | Product tax type (ID) to be used while shipping taxes calculation.
            |
            | The system will select and apply taxes accordingly.
            |
            */
            Config::KEY_SHIPPING_TAX_TYPE_ID => ProductTaxType::SHIPPING_ID,
            /*
            |--------------------------------------------------------------------------
            | Use 'from' address instead of 'to'
            |--------------------------------------------------------------------------
            |
            | While tax calculation system typically uses delivery (to) address for tax
            | calculation. If you want origin (from) address to used set value to true.
            |
            */
            Config::KEY_TAX_ADDRESS_USE_FROM_INSTEADOF_TO => false,
        ]]);

        $this->extendValidator();
    }

    /**
     * Register validation classes.
     */
    private function extendValidator()
    {
        // Validate value for having only characters, numbers, dots, dashes, underscores and spaces.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend('alpha_dash_dot_space', function ($attribute, $value) {
            return preg_match('/^[\pL\pN\s\._-]+$/u', $value);
        }, trans('nm::errors.validation_alpha_dash_dot_space'));

        // Validate value for having only characters, numbers, dots, underscores and dashes.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend('code', function ($attribute, $value) {
            return preg_match('/^[\pL\pN\.\_\-]+$/u', $value);
        }, trans('nm::errors.validation_code'));

        // Attribute marked with this rule must not be in the input data.
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUnusedParameterInspection */
        Validator::extend('forbidden', function () {
            return false;
        }, trans('nm::errors.validation_forbidden'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ImagesInterface::class, Images::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(ImageRepositoryInterface::class, ImageRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(ValueRepositoryInterface::class, ValueRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(VariantRepositoryInterface::class, VariantRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(ImagePathRepositoryInterface::class, ImagePathRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(ImageFormatRepositoryInterface::class, ImageFormatRepository::class);
        $this->app->bind(MeasurementRepositoryInterface::class, MeasurementRepository::class);
        $this->app->bind(OrderStatusRepositoryInterface::class, OrderStatusRepository::class);
        $this->app->bind(SupplyOrderRepositoryInterface::class, SupplyOrderRepository::class);
        $this->app->bind(CustomerRiskRepositoryInterface::class, CustomerRiskRepository::class);
        $this->app->bind(CustomerTypeRepositoryInterface::class, CustomerTypeRepository::class);
        $this->app->bind(EmployeeRoleRepositoryInterface::class, EmployeeRoleRepository::class);
        $this->app->bind(ManufacturerRepositoryInterface::class, ManufacturerRepository::class);
        $this->app->bind(OrderDetailsRepositoryInterface::class, OrderDetailsRepository::class);
        $this->app->bind(OrderInvoiceRepositoryInterface::class, OrderInvoiceRepository::class);
        $this->app->bind(OrderHistoryRepositoryInterface::class, OrderHistoryRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, ProductImageRepository::class);
        $this->app->bind(SpecificationRepositoryInterface::class, SpecificationRepository::class);
        $this->app->bind(ShippingOrderRepositoryInterface::class, ShippingOrderRepository::class);
        $this->app->bind(CharacteristicRepositoryInterface::class, CharacteristicRepository::class);
        $this->app->bind(ProductTaxTypeRepositoryInterface::class, ProductTaxTypeRepository::class);
        $this->app->bind(ProductRelatedRepositoryInterface::class, ProductRelatedRepository::class);
        $this->app->bind(ShippingStatusRepositoryInterface::class, ShippingStatusRepository::class);
        $this->app->bind(CustomerAddressRepositoryInterface::class, CustomerAddressRepository::class);
        $this->app->bind(ImagePropertiesRepositoryInterface::class, ImagePropertiesRepository::class);
        $this->app->bind(OrderStatusRuleRepositoryInterface::class, OrderStatusRuleRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ValuePropertiesRepositoryInterface::class, ValuePropertiesRepository::class);
        $this->app->bind(CountryPropertiesRepositoryInterface::class, CountryPropertiesRepository::class);
        $this->app->bind(ProductPropertiesRepositoryInterface::class, ProductPropertiesRepository::class);
        $this->app->bind(VariantPropertiesRepositoryInterface::class, VariantPropertiesRepository::class);
        $this->app->bind(CategoryPropertiesRepositoryInterface::class, CategoryPropertiesRepository::class);
        $this->app->bind(CurrencyPropertiesRepositoryInterface::class, CurrencyPropertiesRepository::class);
        $this->app->bind(SupplierPropertiesRepositoryInterface::class, SupplierPropertiesRepository::class);
        $this->app->bind(SupplyOrderDetailsRepositoryInterface::class, SupplyOrderDetailsRepository::class);
        $this->app->bind(MeasurementPropertiesRepositoryInterface::class, MeasurementPropertiesRepository::class);
        $this->app->bind(ManufacturerPropertiesRepositoryInterface::class, ManufacturerPropertiesRepository::class);
        $this->app->bind(CharacteristicPropertiesRepositoryInterface::class, CharacteristicPropertiesRepository::class);
    }
}
