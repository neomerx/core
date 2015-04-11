<?php namespace Neomerx\Core\Auth\Permissions;

use \Neomerx\Core\Auth\Permission;

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
