<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Measurement;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Features\MeasurementsInterface;

/**
 * @see MeasurementsInterface
 *
 * @method static Measurement create(array $input)
 * @method static Measurement read(string $code)
 * @method static void        update(string $code, array $input)
 * @method static void        delete(string $code)
 * @method static Collection  all()
 */
class Measurements extends Facade
{
    const INTERFACE_BIND_NAME = MeasurementsInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
