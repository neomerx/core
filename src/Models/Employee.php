<?php namespace Neomerx\Core\Models;

use \Hash;
use \Illuminate\Support\Facades\App;
use \Illuminate\Auth\Authenticatable;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Auth\Passwords\CanResetPassword;
use \Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use \Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * @property int        id_employee
 * @property string     first_name
 * @property string     last_name
 * @property string     email
 * @property string     password
 * @property bool       active
 * @property string     remember_token
 * @property Collection roles
 *
 * @package Neomerx\Core
 */
class Employee extends BaseModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /** Model table name */
    const TABLE_NAME = 'employees';

    /** Model field length */
    const FIRST_NAME_MAX_LENGTH = 50;
    /** Model field length */
    const LAST_NAME_MAX_LENGTH  = 50;
    /** Model field length */
    const EMAIL_MAX_LENGTH      = 100;
    /** Model field length */
    const PASSWORD_MIN_LENGTH   = 8;

    /**
     * Model field length
     *
     * This is not max length for password. The password could be any length.
     * This is the length of the password hash stored in the database.
     */
    const PASSWORD_LENGTH       = 60;
    /** Model field length */
    const REMEMBER_TOKEN_LENGTH = 100;

    /** Input data parameter name for password confirmation */
    const PARAM_PASSWORD_CONFIRMATION = 'password_confirmation';

    /** Model field name */
    const FIELD_ID             = 'id_employee';
    /** Model field name */
    const FIELD_FIRST_NAME     = 'first_name';
    /** Model field name */
    const FIELD_LAST_NAME      = 'last_name';
    /** Model field name */
    const FIELD_EMAIL          = 'email';
    /** Model field name */
    const FIELD_PASSWORD       = 'password';
    /** Model field name */
    const FIELD_ACTIVE         = 'active';
    /** Model field name */
    const FIELD_REMEMBER_TOKEN = 'remember_token';
    /** Model field name */
    const FIELD_ROLES          = 'roles';

    /**
     * @inheritdoc
     */
    protected $table = self::TABLE_NAME;
    /**
     * @inheritdoc
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public $incrementing = true;

    /**
     * @var Role
     */
    private $roleModel;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_ACTIVE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_REMEMBER_TOKEN,
    ];

    /**
     * @inheritdoc
     */
    public function __construct(
        array $attributes = [],
        Role $role = null
    ) {
        parent::__construct($attributes);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->roleModel = ($role !== null ?: App::make(Role::class));
    }

    /**
     * @inheritdoc
     */
    public function getInputOnCreateRules()
    {
        return [
            self::FIELD_FIRST_NAME => 'required|alpha|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME  => 'required|alpha|min:1|max:'.self::LAST_NAME_MAX_LENGTH,
            self::FIELD_EMAIL      => 'required|email|max:'.self::EMAIL_MAX_LENGTH,
            self::FIELD_PASSWORD   => 'required|min:'.self::PASSWORD_MIN_LENGTH.'|confirmed',

            self::PARAM_PASSWORD_CONFIRMATION => 'required|min:'.self::PASSWORD_MIN_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_FIRST_NAME => 'required|alpha|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME  => 'required|alpha|min:1|max:'.self::LAST_NAME_MAX_LENGTH,

            self::FIELD_EMAIL => 'required|email|max:'.self::EMAIL_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_PASSWORD  => 'required|max:'.self::PASSWORD_LENGTH, // hash shouldn't be too long
        ];
    }

    /**
     * @inheritdoc
     */
    public function getInputOnUpdateRules()
    {
        return [
            self::FIELD_FIRST_NAME => 'sometimes|required|alpha|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME  => 'sometimes|required|alpha|min:1|max:'.self::LAST_NAME_MAX_LENGTH,
            self::FIELD_EMAIL      => 'sometimes|required|email|max:'.self::EMAIL_MAX_LENGTH,

            self::FIELD_PASSWORD => 'sometimes|required|min:'.self::PASSWORD_MIN_LENGTH.'|confirmed',

            self::PARAM_PASSWORD_CONFIRMATION => 'required_with:password|min:'.self::PASSWORD_MIN_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_FIRST_NAME => 'sometimes|required|alpha|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME  => 'sometimes|required|alpha|min:1|max:'.self::LAST_NAME_MAX_LENGTH,

            self::FIELD_EMAIL => 'sometimes|required|email|max:'.self::EMAIL_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_PASSWORD => 'sometimes|required|max:'.self::PASSWORD_LENGTH, // hash shouldn't be too long
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withRoles()
    {
        return self::FIELD_ROLES;
    }

    /**
     * Set password. The password would be hashed.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes[self::FIELD_PASSWORD] = Hash::make((string)$value);
    }

    /**
     * Set active attribute.
     *
     * @param $value
     */
    public function setActiveAttribute($value)
    {
        $this->attributes[self::FIELD_ACTIVE] = ($value !== null && (strcasecmp($value, 'on') == 0 || (bool)$value));
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getActiveAttribute($value)
    {
        return (bool)$value;
    }

    /**
     * Relation to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            EmployeeRole::TABLE_NAME,
            EmployeeRole::FIELD_ID_EMPLOYEE,
            EmployeeRole::FIELD_ID_ROLE
        );
    }

    /**
     * Add role to employee.
     *
     * @param Role $role
     *
     * @return bool
     */
    public function addRole(Role $role)
    {
        $this->roles()->save($role);
        return $role->exists;
    }

    /**
     * Remove role from employee.
     *
     * @param Role $role
     *
     * @return bool
     */
    public function removeRole(Role $role)
    {
        return $this->roles()->detach($role->{Role::FIELD_ID}) > 0;
    }

    /**
     * @param int $employeeId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectById($employeeId)
    {
        return $this->newQuery()->where(self::FIELD_ID, '=', $employeeId);
    }
}
