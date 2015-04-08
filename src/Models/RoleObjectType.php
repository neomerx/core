<?php namespace Neomerx\Core\Models;

/**
 * @property int        id_role_object_type
 * @property int        id_action
 * @property int        id_role
 * @property ObjectType type
 * @property Role       role
 */
class RoleObjectType extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'roles_object_types';

    const FIELD_ID         = 'id_role_object_type';
    const FIELD_ID_TYPE    = ObjectType::FIELD_ID;
    const FIELD_ID_ROLE    = Role::FIELD_ID;
    const FIELD_ALLOW_MASK = 'allow_mask';
    const FIELD_DENY_MASK  = 'deny_mask';
    const FIELD_TYPE       = 'type';
    const FIELD_ROLE       = 'role';

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
    protected $fillable = [
        self::FIELD_ALLOW_MASK,
        self::FIELD_DENY_MASK,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_TYPE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TYPE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_TYPE    => 'required|integer|min:1|max:4294967295|exists:'.ObjectType::TABLE_NAME,
            self::FIELD_ID_ROLE    => 'required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
            self::FIELD_ALLOW_MASK => 'required|integer|min:0|max:4294967295',
            self::FIELD_DENY_MASK  => 'sometimes|required|integer|min:0|max:4294967295',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_TYPE    => 'sometimes|required|integer|min:1|max:4294967295|exists:'.ObjectType::TABLE_NAME,
            self::FIELD_ID_ROLE    => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
            self::FIELD_ALLOW_MASK => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_DENY_MASK  => 'sometimes|required|integer|min:0|max:4294967295',
        ];
    }

    /**
     * @return string
     */
    public static function withType()
    {
        return self::FIELD_TYPE;
    }

    /**
     * @return string
     */
    public static function withRole()
    {
        return self::FIELD_ROLE;
    }

    /**
     * Relation to object type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ObjectType::BIND_NAME, self::FIELD_ID_TYPE, ObjectType::FIELD_ID);
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
