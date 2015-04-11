<?php namespace Neomerx\Core\Auth\Permissions;

use \Neomerx\Core\Auth\Permission;

class CreatePermission extends Permission
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::CREATE);
    }
}
