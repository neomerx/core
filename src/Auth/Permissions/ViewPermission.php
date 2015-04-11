<?php namespace Neomerx\Core\Auth\Permissions;

use \Neomerx\Core\Auth\Permission;

class ViewPermission extends Permission
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::VIEW);
    }
}
