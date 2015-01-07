<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Features;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Converters\CharacteristicConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;
use \Neomerx\Core\Converters\CharacteristicValueConverterGeneric;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class FeaturesControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Features::INTERFACE_BIND_NAME, App::make(CharacteristicConverterGeneric::BIND_NAME));
    }

    /**
     * Search features.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input, $this->getLanguageFilter($input)]);
    }

    /**
     * Get all values for characteristic with code $characteristicCode.
     *
     * @param string $characteristicCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function allValues($characteristicCode)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper(
            'allValuesImpl',
            [$characteristicCode, $this->getLanguageFilter(Input::all())]
        );
    }

    /**
     * Add values to characteristic with code $characteristicCode.
     *
     * @param string $characteristicCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function addValues($characteristicCode)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('addValuesImpl', [$characteristicCode, $input]);
    }

    /**
     * Read value by code.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function readValue($code)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('readValueImpl', [$code, $this->getLanguageFilter(Input::all())]);
    }

    /**
     * Update characteristic value.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateValue($code)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('updateValueImpl', [$code, $input]);
    }

    /**
     * Delete characteristic value.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function deleteValue($code)
    {
        return $this->tryAndCatchWrapper('deleteValueImpl', [$code]);
    }

    /**
     * @param array  $parameters
     * @param string $languageFilter
     *
     * @return array
     */
    protected function searchImpl(array $parameters, $languageFilter)
    {
        // TODO check all 'search' and 'all' methods if they use lang filter

        /** @var CharacteristicConverterGeneric $converter */
        $converter = $this->getConverter();
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($this->getApiFacade()->search($parameters) as $resource) {
            $result[] = $converter->convert($resource);
        }

        return [$result, null];
    }

    /**
     * @param string $characteristicCode
     *
     * @param string $languageFilter
     *
     * @return array
     */
    protected function allValuesImpl($characteristicCode, $languageFilter)
    {
        $characteristic = $this->getModelByCode(Characteristic::BIND_NAME, $characteristicCode);

        /** @var CharacteristicValueConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(CharacteristicValueConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($this->getApiFacade()->allValues($characteristic) as $resource) {
            $result[] = $converter->convert($resource);
        }

        return [$result, null];
    }

    /**
     * @param string $characteristicCode
     * @param array  $input
     *
     * @return array
     */
    protected function addValuesImpl($characteristicCode, array $input)
    {
        $this->getApiFacade()->addValues($this->getModelByCode(Characteristic::BIND_NAME, $characteristicCode), $input);
        return [null, SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param string $code
     * @param string $languageFilter
     *
     * @return array
     */
    protected function readValueImpl($code, $languageFilter)
    {
        $resource = $this->getApiFacade()->readValue($code);

        /** @var CharacteristicValueConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(CharacteristicValueConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);

        return [$converter->convert($resource), null];
    }

    /**
     * @param string $code
     * @param array  $input
     *
     * @return array
     */
    protected function updateValueImpl($code, array $input)
    {
        return [$this->getApiFacade()->updateValue($code, $input), null];
    }

    /**
     * @param string $code
     *
     * @return array
     */
    protected function deleteValueImpl($code)
    {
        return [$this->getApiFacade()->deleteValue($code), null];
    }
}
