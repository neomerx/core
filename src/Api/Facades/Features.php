<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Characteristic;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Models\CharacteristicValue;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Features\FeaturesInterface;

/**
 * @see FeaturesInterface
 *
 * @method static Characteristic      create(array $input)
 * @method static Characteristic      read(string $code)
 * @method static void                update(string $code, array $input)
 * @method static void                delete(string $code)
 * @method static Collection          search(array $parameters = [])
 * @method static Collection          allValues(string $characteristicCode)
 * @method static void                addValues(string $characteristicCode, array $input)
 * @method static CharacteristicValue readValue(string $code)
 * @method static void                updateValue(string $code, array $input)
 * @method static void                deleteValue(string $code)
 */
class Features extends Facade
{
    const INTERFACE_BIND_NAME = FeaturesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
