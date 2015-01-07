<?php namespace Neomerx\Core\Auth;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Permission
{
    const VIEW     = 1;
    const CREATE   = 2;
    const EDIT     = 4;
    const DELETE   = 8;
    const RESTORE  = 16;
    const OPERATOR = 32;
    const MASTER   = 64;
    const OWNER    = 128;

    /**
     * @var int
     */
    private $mask;

    /**
     * @var Permission
     */
    private static $privilegeView;

    /**
     * @var Permission
     */
    private static $privilegeEdit;

    /**
     * @var Permission
     */
    private static $privilegeCreate;

    /**
     * @var Permission
     */
    private static $privilegeDelete;

    /**
     * @var Permission
     */
    private static $privilegeRestore;

    /**
     * @var Permission
     */
    private static $privilegeCanView;

    /**
     * @var Permission
     */
    private static $privilegeCanEdit;

    /**
     * @var Permission
     */
    private static $privilegeCanCreate;

    /**
     * @var Permission
     */
    private static $privilegeCanDelete;

    /**
     * @var Permission
     */
    private static $privilegeCanRestore;

    /**
     * @param int $mask
     */
    public function __construct($mask)
    {
        settype($mask, 'int');
        $this->mask = $mask;
    }

    /**
     * Check if permission has flags specified by $mask.
     *
     * @param int $mask
     *
     * @return bool
     */
    public function has($mask)
    {
        settype($mask, 'int');
        return $this->mask & $mask ? true : false;
    }

    /**
     * Get 'view' permission.
     *
     * @return Permission
     */
    public static function view()
    {
        return self::$privilegeView ?: (self::$privilegeView = new Permission(self::VIEW));
    }

    /**
     * Get 'edit' permission.
     *
     * @return Permission
     */
    public static function edit()
    {
        return self::$privilegeEdit ?: (self::$privilegeEdit = new Permission(self::EDIT));
    }

    /**
     * Get 'create' permission.
     *
     * @return Permission
     */
    public static function create()
    {
        return self::$privilegeCreate ?: (self::$privilegeCreate = new Permission(self::CREATE));
    }

    /**
     * Get 'delete' permission.
     *
     * @return Permission
     */
    public static function delete()
    {
        return self::$privilegeDelete ?: (self::$privilegeDelete = new Permission(self::DELETE));
    }

    /**
     * Get 'restore' permission.
     *
     * @return Permission
     */
    public static function restore()
    {
        return self::$privilegeRestore ?: (self::$privilegeRestore = new Permission(self::RESTORE));
    }

    /**
     * Get description of those who has 'view' permission.
     *
     * @return Permission
     */
    public static function canView()
    {
        return self::$privilegeCanView ?: (self::$privilegeCanView = new Permission(
            self::VIEW | self::EDIT | self::OPERATOR | self::MASTER | self::OWNER
        ));
    }

    /**
     * Get description of those who has 'edit' permission.
     *
     * @return Permission
     */
    public static function canEdit()
    {
        return self::$privilegeCanEdit ?: (self::$privilegeCanEdit = new Permission(
            self::EDIT | self::OPERATOR | self::MASTER | self::OWNER
        ));
    }

    /**
     * Get description of those who has 'create' permission.
     *
     * @return Permission
     */
    public static function canCreate()
    {
        return self::$privilegeCanCreate ?: (self::$privilegeCanCreate = new Permission(
            self::CREATE | self::OPERATOR | self::MASTER | self::OWNER
        ));
    }

    /**
     * Get description of those who has 'delete' permission.
     *
     * @return Permission
     */
    public static function canDelete()
    {
        return self::$privilegeCanDelete ?: (self::$privilegeCanDelete = new Permission(
            self::DELETE | self::OPERATOR | self::MASTER | self::OWNER
        ));
    }

    /**
     * Get description of those who has 'restore' permission.
     *
     * @return Permission
     */
    public static function canRestore()
    {
        return self::$privilegeCanRestore ?: (self::$privilegeCanRestore = new Permission(
            self::RESTORE | self::OPERATOR | self::MASTER | self::OWNER
        ));
    }
}
