<?php namespace Neomerx\Core\Api\Currencies;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Currency as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CurrencyProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Currencies implements CurrenciesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Currency.';
    const BIND_NAME = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var PropertiesModel
     */
    private $properties;

    /**
     * @var LanguageModel
     */
    private $language;

    /**
     * @param Model           $model
     * @param PropertiesModel $properties
     * @param LanguageModel   $language
     */
    public function __construct(Model $model, PropertiesModel $properties, LanguageModel $language)
    {
        $this->model      = $model;
        $this->properties = $properties;
        $this->language   = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        // check language properties are not empty
        count($propertiesInput) ? null : S\throwEx(new InvalidArgumentException('properties'));

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $currency */
            $currency = $this->model->createOrFailResource($input);
            Permissions::check($currency, Permission::create());

            $currencyId = $currency->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge(
                    [Model::FIELD_ID => $currencyId, LanguageModel::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CurrencyArgs(self::EVENT_PREFIX . 'created', $currency));

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $currency */
        $currency = $this->model->selectByCode($code)->withProperties()->firstOrFail();

        Permissions::check($currency, Permission::view());

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $currency */
            $currency = $this->model->selectByCode($code)->firstOrFail();

            Permissions::check($currency, Permission::edit());

            empty($input) ?: $currency->updateOrFail($input);

            $currencyId = $currency->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var PropertiesModel $property */
                $property = $this->properties->updateOrCreate(
                    [Model::FIELD_ID => $currencyId, LanguageModel::FIELD_ID => $languageId],
                    $propertyInput
                );
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CurrencyArgs(self::EVENT_PREFIX . 'updated', $currency));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Model $currency */
        $currency = $this->model->selectByCode($code)->firstOrFail();

        Permissions::check($currency, Permission::delete());

        $currency->deleteOrFail();

        Event::fire(new CurrencyArgs(self::EVENT_PREFIX . 'deleted', $currency));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $currencies = $this->model->newQuery()->withProperties()->get();

        foreach ($currencies as $currency) {
            /** @var Model $currency */
            Permissions::check($currency, Permission::view());
        }

        return $currencies;
    }
}
