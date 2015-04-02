<?php

use \Neomerx\Core\Config;
use \Neomerx\Core\Models\ProductTaxType;

return [

    /*
    |--------------------------------------------------------------------------
    | Image disk
    |--------------------------------------------------------------------------
    |
    | The disk is used for uploading product images and storing them in
    | various image formats.
    |
    | This disk should be writable for the web server.
    |
    */
    Config::KEY_IMAGE_DISK => 'images',

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

];
