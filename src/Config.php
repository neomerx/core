<?php namespace Neomerx\Core;

use \Config as ConfigFacade;
use \Neomerx\Core\Support\CoreServiceProvider;

class Config
{
    const CONFIG_FILE_NAME_WO_EXT = CoreServiceProvider::PACKAGE_NAMESPACE;

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
        return array_get(ConfigFacade::get(self::CONFIG_FILE_NAME_WO_EXT), $key);
    }
}
