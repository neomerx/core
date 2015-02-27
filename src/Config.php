<?php namespace Neomerx\Core;

use \Neomerx\Core\Support\CoreServiceProvider;
use \Illuminate\Support\Facades\Config as ConfigFacade;

class Config
{
    /** Disk for storing image files */
    const KEY_IMAGE_DISK = 'images';

    /** Folder name for storing original image files */
    const KEY_IMAGE_FOLDER_ORIGINALS = 'originals';

    /** Folder name for storing formatted image files */
    const KEY_IMAGE_FOLDER_FORMATS = 'formats';

    /** Product tax type for shipping (ID) */
    const KEY_SHIPPING_TAX_TYPE_ID = 'shippingTaxTypeId';

    /** Use customer's billing address instead of shipping while tax calculation */
    const KEY_TAX_ADDRESS_USE_FROM_INSTEADOF_TO = 'calcTaxUseFromAddress';

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return ConfigFacade::get(CoreServiceProvider::CONFIG_ROOT_KEY)[$key];
    }
}
