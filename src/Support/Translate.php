<?php namespace Neomerx\Core\Support;

/**
 * @package Neomerx\Core
 */
class Translate
{
    /** Translation file name without .php extension */
    const FILE_NAME_WO_EXT = 'application';

    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_GENERAL             = 'customer_type_general';
    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_MEMBER              = 'customer_type_member';
    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_GUEST               = 'customer_type_guest';
    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_PRIVATE             = 'customer_type_private';
    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_RETAIL              = 'customer_type_retail';
    /** Translation key */
    const KEY_MSG_CUSTOMER_TYPE_WHOLESALE           = 'customer_type_wholesale';
    /** Translation key */
    const KEY_MSG_ORDER_STATUS_NEW                  = 'order_status_new';
    /** Translation key */
    const KEY_MSG_PRODUCT_TAX_TYPE_SHIPPING         = 'product_tax_type_shipping';
    /** Translation key */
    const KEY_MSG_PRODUCT_TAX_TYPE_EXEMPT           = 'product_tax_type_exempt';
    /** Translation key */
    const KEY_MSG_PRODUCT_TAX_TYPE_TAXABLE          = 'product_tax_type_taxable';
    /** Translation key */
    const KEY_MSG_STORE_DEFAULT_NAME                = 'store_default_name';
    /** Translation key */
    const KEY_MSG_WAREHOUSE_DEFAULT_NAME            = 'warehouse_default_name';

    /** Translation key */
    const KEY_ERR_VALIDATION_FORBIDDEN              = 'validation_forbidden';
    /** Translation key */
    const KEY_ERR_VALIDATION_ALPHA_DASH_DOT_SPACE   = 'validation_alpha_dash_dot_space';
    /** Translation key */
    const KEY_ERR_VALIDATION_CODE                   = 'validation_code';

    /** Translation key */
    const KEY_EX_ACCESS_DENIED_FILE_EXCEPTION       = 'access_denied_file_exception';
    /** Translation key */
    const KEY_EX_EXCEPTION                          = 'exception';
    /** Translation key */
    const KEY_EX_FILE_EXCEPTION                     = 'file_exception';
    /** Translation key */
    const KEY_EX_INVALID_ARGUMENT_EXCEPTION         = 'invalid_argument_exception';
    /** Translation key */
    const KEY_EX_LOGIC_EXCEPTION                    = 'logic_exception';
    /** Translation key */
    const KEY_EX_VALIDATION_EXCEPTION               = 'validation_exception';
    /** Translation key */
    const KEY_EX_ACCESS_DENIED_EXCEPTION            = 'access_denied';

    /**
     * Get translated message.
     *
     * @param $key
     *
     * @return string
     */
    public static function trans($key)
    {
        return trans(CoreServiceProvider::PACKAGE_NAMESPACE.'::'.self::FILE_NAME_WO_EXT.'.'.$key);
    }
}
