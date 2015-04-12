<?php

use \Neomerx\Core\Support\Translate as T;

return [

    /*
     * Application
     */
    T::KEY_MSG_CUSTOMER_TYPE_GENERAL             => 'General',
    T::KEY_MSG_CUSTOMER_TYPE_MEMBER              => 'Member',
    T::KEY_MSG_CUSTOMER_TYPE_GUEST               => 'Guest/not logged-in',
    T::KEY_MSG_CUSTOMER_TYPE_PRIVATE             => 'Private',
    T::KEY_MSG_CUSTOMER_TYPE_RETAIL              => 'Retail',
    T::KEY_MSG_CUSTOMER_TYPE_WHOLESALE           => 'Wholesale',

    T::KEY_MSG_ORDER_STATUS_NEW                  => 'New order',

    T::KEY_MSG_PRODUCT_TAX_TYPE_SHIPPING         => 'Shipping',
    T::KEY_MSG_PRODUCT_TAX_TYPE_EXEMPT           => 'Exempt',
    T::KEY_MSG_PRODUCT_TAX_TYPE_TAXABLE          => 'Taxable',

    T::KEY_MSG_STORE_DEFAULT_NAME                => 'Store name',

    T::KEY_MSG_WAREHOUSE_DEFAULT_NAME            => 'Warehouse name',

    /*
     * Errors
     */

    T::KEY_ERR_VALIDATION_FORBIDDEN              => 'The :attribute must not be specified.',
    T::KEY_ERR_VALIDATION_ALPHA_DASH_DOT_SPACE   => 'The :attribute may only contain letters, numbers, dashes, '.
                                                    'dots, underscores and spaces.',
    T::KEY_ERR_VALIDATION_CODE                   => 'The :attribute may only contain letters, numbers, dashes, '.
                                                    'dots and underscores.',

    /*
     * Exceptions
     */

    T::KEY_EX_ACCESS_DENIED_FILE_EXCEPTION       => 'Access to file denied.',
    T::KEY_EX_EXCEPTION                          => 'Unknown error.',
    T::KEY_EX_FILE_EXCEPTION                     => 'Error occurred while working with file.',
    T::KEY_EX_INVALID_ARGUMENT_EXCEPTION         => 'Invalid argument provided.',
    T::KEY_EX_LOGIC_EXCEPTION                    => 'Error occurred in program logic.',
    T::KEY_EX_VALIDATION_EXCEPTION               => 'Validation failed.',
    T::KEY_EX_ACCESS_DENIED_EXCEPTION            => 'Access denied.',

];
