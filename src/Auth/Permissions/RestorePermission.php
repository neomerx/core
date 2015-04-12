<?php namespace Neomerx\Core\Auth\Permissions;

use \Neomerx\Core\Auth\Permission;

/**
 * @package Neomerx\Core
 */
class RestorePermission extends Permission
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::RESTORE);
    }
}
