<?php namespace Neomerx\Core\Api\Currencies;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CurrencyProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Currencies implements CurrenciesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Currency.';
    const BIND_NAME = __CLASS__;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var CurrencyProperties
     */
    private $properties;

    /**
     * @var Language
     */
    private $language;

    /**
     * @param Currency           $currency
     * @param CurrencyProperties $properties
     * @param Language           $language
     */
    public function __construct(Currency $currency, CurrencyProperties $properties, Language $language)
    {
        $this->currency   = $currency;
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

            /** @var \Neomerx\Core\Models\Currency $currency */
            $currency = $this->currency->createOrFailResource($input);
            Permissions::check($currency, Permission::create());

            $currencyId = $currency->{Currency::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge($propertyInput, [
                    CurrencyProperties::FIELD_ID_CURRENCY => $currencyId,
                    CurrencyProperties::FIELD_ID_LANGUAGE => $languageId
                ]));
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
        /** @var \Neomerx\Core\Models\Currency $currency */
        $currency = $this->currency->selectByCode($code)->withProperties()->firstOrFail();

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

            /** @var \Neomerx\Core\Models\Currency $currency */
            $currency = $this->currency->selectByCode($code)->firstOrFail();

            Permissions::check($currency, Permission::edit());

            empty($input) ?: $currency->updateOrFail($input);

            $currencyId = $currency->{Currency::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var CurrencyProperties $property */
                $property = $this->properties->updateOrCreate([
                    CurrencyProperties::FIELD_ID_CURRENCY => $currencyId,
                    CurrencyProperties::FIELD_ID_LANGUAGE => $languageId
                ], $propertyInput);
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
        /** @var \Neomerx\Core\Models\Currency $currency */
        $currency = $this->currency->selectByCode($code)->firstOrFail();

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
        $currencies = $this->currency->newQuery()->withProperties()->get();

        foreach ($currencies as $currency) {
            /** @var \Neomerx\Core\Models\Currency $currency */
            Permissions::check($currency, Permission::view());
        }

        return $currencies;
    }
}
