<?php namespace Neomerx\Core\Auth;

use \Neomerx\Core\Auth\Permissions\EditPermission;
use \Neomerx\Core\Auth\Permissions\ViewPermission;
use \Neomerx\Core\Auth\Permissions\DeletePermission;
use \Neomerx\Core\Auth\Permissions\CreatePermission;
use \Neomerx\Core\Auth\Permissions\RestorePermission;

/**
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class Permission
{
    /** Permission code */
    const VIEW     = 1;
    /** Permission code */
    const CREATE   = 2;
    /** Permission code */
    const EDIT     = 4;
    /** Permission code */
    const DELETE   = 8;
    /** Permission code */
    const RESTORE  = 16;

    /**
     * @var int
     */
    private $permissionMask;

    /**
     * @param int $permissionMask
     */
    public function __construct($permissionMask)
    {
        settype($permissionMask, 'int');
        $this->permissionMask = $permissionMask;
    }

    /**
     * Determine if all permission bits can pass the permissions specified in the mask.
     *
     * @param Permission $permission
     * @param int        $allowedMask
     *
     * @return bool
     */
    public static function canPass(Permission $permission, $allowedMask)
    {
        settype($allowedMask, 'int');
        return (($permission->getPermissionMask() & $allowedMask) === $permission->getPermissionMask());
    }

    /**
     * Get 'view' permission.
     *
     * @return ViewPermission
     */
    public static function view()
    {
        return new ViewPermission();
    }

    /**
     * Get 'edit' permission.
     *
     * @return EditPermission
     */
    public static function edit()
    {
        return new EditPermission();
    }

    /**
     * Get 'create' permission.
     *
     * @return CreatePermission
     */
    public static function create()
    {
        return new CreatePermission();
    }

    /**
     * Get 'delete' permission.
     *
     * @return DeletePermission
     */
    public static function delete()
    {
        return new DeletePermission();
    }

    /**
     * Get 'restore' permission.
     *
     * @return RestorePermission
     */
    public static function restore()
    {
        return new RestorePermission();
    }

    /**
     * @return int
     */
    public function getPermissionMask()
    {
        return $this->permissionMask;
    }
}
