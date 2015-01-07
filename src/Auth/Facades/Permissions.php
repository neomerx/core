<?php namespace Neomerx\Core\Auth\Facades;

use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Auth\ObjectIdentityInterface;
use \Neomerx\Core\Auth\PermissionManagementInterface;

/**
 * @see PermissionManagementInterface
 *
 * @method static bool has(ObjectIdentityInterface $object, Permission $permission)
 * @method static void check(ObjectIdentityInterface $object, Permission $permission)
 */
class Permissions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PermissionManagementInterface::class;
    }
}
