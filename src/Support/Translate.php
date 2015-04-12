<?php namespace Neomerx\Core\Support;

/**
 * @package Neomerx\Core
 */
class Translate
{
    /** Translation file name without .php extension */
    const FILE_NAME_WO_EXT = 'application';

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
