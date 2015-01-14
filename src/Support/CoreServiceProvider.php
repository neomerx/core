<?php namespace Neomerx\Core\Support;

use \Illuminate\Support\ServiceProvider;
use \Illuminate\Support\Facades\Validator;
use Neomerx\Core\Repositories\AddressRepository;
use Neomerx\Core\Repositories\AddressRepositoryInterface;
use \Neomerx\Core\Repositories\CountryRepository;
use Neomerx\Core\Repositories\LanguageRepository;
use Neomerx\Core\Repositories\LanguageRepositoryInterface;
use \Neomerx\Core\Repositories\ProductRepository;
use Neomerx\Core\Repositories\RegionRepository;
use Neomerx\Core\Repositories\RegionRepositoryInterface;
use \Neomerx\Core\Repositories\VariantRepository;
use \Neomerx\Core\Repositories\OrderDetailsRepository;
use \Neomerx\Core\Repositories\SpecificationRepository;
use \Neomerx\Core\Repositories\ImagePropertiesRepository;
use \Neomerx\Core\Repositories\CountryRepositoryInterface;
use \Neomerx\Core\Repositories\ProductRepositoryInterface;
use \Neomerx\Core\Repositories\VariantRepositoryInterface;
use \Neomerx\Core\Repositories\CountryPropertiesRepository;
use \Neomerx\Core\Repositories\OrderDetailsRepositoryInterface;
use \Neomerx\Core\Repositories\SpecificationRepositoryInterface;
use \Neomerx\Core\Repositories\ImagePropertiesRepositoryInterface;
use \Neomerx\Core\Repositories\CountryPropertiesRepositoryInterface;

/**
 * Provides necessary Neomerx registrations in a single location. It bootstraps
 * such components as facades, validation rules and etc.
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
        $resourceDir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
            '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;

        $this->package('neomerx/core', self::NEOMERX_PREFIX, $resourceDir);
        /** @noinspection PhpIncludeInspection */
        include $resourceDir.'filters.php';
        /** @noinspection PhpIncludeInspection */
        include $resourceDir.'routes.php';

        $this->extendValidator();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(VariantRepositoryInterface::class, VariantRepository::class);
        $this->app->bind(SpecificationRepositoryInterface::class, SpecificationRepository::class);
        $this->app->bind(ImagePropertiesRepositoryInterface::class, ImagePropertiesRepository::class);
        $this->app->bind(OrderDetailsRepositoryInterface::class, OrderDetailsRepository::class);
        $this->app->bind(CountryPropertiesRepositoryInterface::class, CountryPropertiesRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
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
}
