<?php namespace Neomerx\Core;

use \Neomerx\Core\Support\CoreServiceProvider;
use \Illuminate\Support\Facades\Config as ConfigFacade;

class Config
{
    const FILE_APP    = 'app';
    const FILE_EVENTS = 'events';

    /** Folder name for storing image files */
    const KEY_IMAGE_FOLDER = 'imageFolder';

    /** Product tax type for shipping (ID) */
    const KEY_SHIPPING_TAX_TYPE_ID = 'shippingTaxTypeId';

    /** Use customer's billing address instead of shipping while tax calculation */
    const KEY_TAX_ADDRESS_USE_FROM_INSTEADOF_TO = 'calcTaxUseFromAddress';

    /**
     * @param string $file
     * @param string $key
     *
     * @return mixed
     */
    public static function get($file, $key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return ConfigFacade::get(CoreServiceProvider::NEOMERX_PREFIX.'::'.$file.'.'.$key);
    }
}
