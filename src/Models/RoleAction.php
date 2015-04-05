<?php namespace Neomerx\Core\Models;

/**
 * @property int    id_role_action
 * @property int    id_action
 * @property int    id_role
 * @property Action action
 * @property Role   role
 */
class RoleAction extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'roles_actions';

    const FIELD_ID        = 'id_role_action';
    const FIELD_ID_ACTION = Action::FIELD_ID;
    const FIELD_ID_ROLE   = Role::FIELD_ID;
    const FIELD_ACTION    = 'action';
    const FIELD_ROLE      = 'role';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_ACTION,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ACTION,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ACTION => 'required|integer|min:1|max:4294967295|exists:'.Action::TABLE_NAME,
            self::FIELD_ID_ROLE   => 'required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ACTION => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Action::TABLE_NAME,
            self::FIELD_ID_ROLE   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withAction()
    {
        return self::FIELD_ACTION;
    }

    /**
     * @return string
     */
    public static function withRole()
    {
        return self::FIELD_ROLE;
    }

    /**
     * Relation to action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo(Action::BIND_NAME, self::FIELD_ID_ACTION, Action::FIELD_ID);
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
