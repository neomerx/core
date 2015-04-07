<?php

use \Neomerx\Core\Support\Translate as T;

return [

    /*
     * Application
     */


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
