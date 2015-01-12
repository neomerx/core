<?php namespace Neomerx\Core\Models;

/**
 * @property int  id_user_role
 * @property int  id_user
 * @property int  id_role
 * @property User user
 * @property Role role
 */
class UserRole extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'users_roles';

    const FIELD_ID      = 'id_user_role';
    const FIELD_ID_USER = User::FIELD_ID;
    const FIELD_ID_ROLE = Role::FIELD_ID;
    const FIELD_USER    = 'user';
    const FIELD_ROLE    = 'role';

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
    public $incrementing = true;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_USER,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_USER,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_USER => 'required|integer|min:1|max:4294967295|exists:' . User::TABLE_NAME,
            self::FIELD_ID_ROLE => 'required|integer|min:1|max:4294967295|exists:' . Role::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_USER => 'sometimes|required|integer|min:1|max:4294967295|exists:' . User::TABLE_NAME,
            self::FIELD_ID_ROLE => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Role::TABLE_NAME,
        ];
    }

    /**
     * Relation to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::BIND_NAME, self::FIELD_ID_USER, User::FIELD_ID);
    }

    /**
     * Relation to role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::BIND_NAME, self::FIELD_ID_ROLE, Role::FIELD_ID);
    }
}
