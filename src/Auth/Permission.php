<?php namespace Neomerx\Core\Auth;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class Permission
{
    const VIEW     = 1;
    const CREATE   = 2;
    const EDIT     = 4;
    const DELETE   = 8;
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
