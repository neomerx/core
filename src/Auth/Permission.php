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
//    const OPERATOR = 32;
//    const MASTER   = 64;
//    const OWNER    = 128;

    /**
     * @var int
     */
    private $permissionMask;

//    /**
//     * @var Permission
//     */
//    private static $privilegeView;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeEdit;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCreate;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeDelete;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeRestore;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCanView;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCanEdit;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCanCreate;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCanDelete;
//
//    /**
//     * @var Permission
//     */
//    private static $privilegeCanRestore;

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
     * @return Permission
     */
    public static function view()
    {
        return new ViewPermission();
    }

    /**
     * Get 'edit' permission.
     *
     * @return Permission
     */
    public static function edit()
    {
        return new EditPermission();
    }

    /**
     * Get 'create' permission.
     *
     * @return Permission
     */
    public static function create()
    {
        return new CreatePermission();
    }

    /**
     * Get 'delete' permission.
     *
     * @return Permission
     */
    public static function delete()
    {
        return new DeletePermission();
    }

    /**
     * Get 'restore' permission.
     *
     * @return Permission
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
//
//    /**
//     * Get description of those who has 'view' permission.
//     *
//     * @return Permission
//     */
//    public static function canView()
//    {
//        self::$privilegeCanView !== null ?: (self::$privilegeCanView = new Permission(
//            self::VIEW | self::EDIT | self::OPERATOR | self::MASTER | self::OWNER
//        ));
//        return self::$privilegeCanView;
//    }
//
//    /**
//     * Get description of those who has 'edit' permission.
//     *
//     * @return Permission
//     */
//    public static function canEdit()
//    {
//        self::$privilegeCanEdit !== null ?: (self::$privilegeCanEdit = new Permission(
//            self::EDIT | self::OPERATOR | self::MASTER | self::OWNER
//        ));
//        return self::$privilegeCanEdit;
//    }
//
//    /**
//     * Get description of those who has 'create' permission.
//     *
//     * @return Permission
//     */
//    public static function canCreate()
//    {
//        self::$privilegeCanCreate !== null ?: (self::$privilegeCanCreate = new Permission(
//            self::CREATE | self::OPERATOR | self::MASTER | self::OWNER
//        ));
//        return self::$privilegeCanCreate;
//    }
//
//    /**
//     * Get description of those who has 'delete' permission.
//     *
//     * @return Permission
//     */
//    public static function canDelete()
//    {
//        self::$privilegeCanDelete !== null ?: (self::$privilegeCanDelete = new Permission(
//            self::DELETE | self::OPERATOR | self::MASTER | self::OWNER
//        ));
//        return self::$privilegeCanDelete;
//    }
//
//    /**
//     * Get description of those who has 'restore' permission.
//     *
//     * @return Permission
//     */
//    public static function canRestore()
//    {
//        self::$privilegeCanRestore !== null ?: (self::$privilegeCanRestore = new Permission(
//            self::RESTORE | self::OPERATOR | self::MASTER | self::OWNER
//        ));
//        return self::$privilegeCanRestore;
//    }
}
