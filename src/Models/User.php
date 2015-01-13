<?php namespace Neomerx\Core\Models;

use \Hash;
use \Illuminate\Auth\UserTrait;
use \Illuminate\Auth\UserInterface;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Auth\Reminders\RemindableTrait;
use \Illuminate\Auth\Reminders\RemindableInterface;

/**
 * @property int        id_user
 * @property string     first_name
 * @property string     last_name
 * @property string     email
 * @property string     password
 * @property bool       active
 * @property string     remember_token
 * @property Collection roles
 * @method   Builder    withRoles()
 */
class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait;

    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'users';

    const FIRST_NAME_MAX_LENGTH = 50;
    const LAST_NAME_MAX_LENGTH  = 50;
    const EMAIL_MAX_LENGTH      = 100;
    const PASSWORD_MIN_LENGTH   = 8;

    /**
     * This is not max length for password. The password could be any length.
     * This is the length of the password hash stored in the database.
     */
    const PASSWORD_LENGTH       = 60;
    const REMEMBER_TOKEN_LENGTH = 100;

    const PARAM_PASSWORD_CONFIRMATION = 'password_confirmation';

    const FIELD_ID             = 'id_user';
    const FIELD_FIRST_NAME     = 'first_name';
    const FIELD_LAST_NAME      = 'last_name';
    const FIELD_EMAIL          = 'email';
    const FIELD_PASSWORD       = 'password';
    const FIELD_ACTIVE         = 'active';
    const FIELD_REMEMBER_TOKEN = 'remember_token';
    const FIELD_ROLES          = 'roles';

    /**
     * {@inheritdoc}
     */
    protected $table = self::TABLE_NAME;
    /**
     * {@inheritdoc}
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * @var Role
     */
    private $roleModel;
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_ACTIVE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_REMEMBER_TOKEN,
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $attributes = [],
        Role $role = null
    ) {
        parent::__construct($attributes);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->roleModel = $role ?: App::make(Role::BIND_NAME);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithRoles(Builder $query)
    {
        return $query->with([self::FIELD_ROLES]);
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
        $this->attributes[self::FIELD_ACTIVE] = ($value !== null and (strcasecmp($value, 'on') == 0 or (bool)$value));
    }

    /**
     * Relation to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::BIND_NAME,
            UserRole::TABLE_NAME,
            UserRole::FIELD_ID_USER,
            UserRole::FIELD_ID_ROLE
        );
    }

    /**
     * Add role to user.
     *
     * @param string $code
     *
     * @return bool
     */
    public function addRole($code)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\Role $role */
        $role = $this->roleModel->selectByCode($code)->firstOrFail([Role::FIELD_ID]);
        $this->roles()->save($role);
        return $role->exists;
    }

    /**
     * Remove role to user.
     *
     * @param $code
     *
     * @return bool
     */
    public function removeRole($code)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $roleId = $this->roleModel->selectByCode($code)->firstOrFail([Role::FIELD_ID])->{Role::FIELD_ID};
        return $this->roles()->detach($roleId) > 0;
    }

    /**
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectById($userId)
    {
        return $this->newQuery()->where(self::FIELD_ID, '=', $userId);
    }
}
